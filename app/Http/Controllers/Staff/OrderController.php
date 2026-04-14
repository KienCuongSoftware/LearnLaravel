<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusChangedMail;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StaffActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $shippingStatus = $request->query('shipping_status', 'all');
        $q = trim((string) $request->query('q', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = $this->filteredOrdersQuery($request);

        $orders = $query->paginate(7)->withQueryString();
        session(['staff.orders.page' => $orders->currentPage()]);

        return view('staff.orders.index', compact(
            'orders',
            'status',
            'shippingStatus',
            'q',
            'dateFrom',
            'dateTo'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filteredOrdersQuery($request)->latest();

        $filename = 'orders-export-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['id', 'created_at', 'customer_email', 'customer_name', 'status', 'shipping_status', 'payment_method', 'total_amount', 'phone']);

            $query->chunk(200, function ($chunk) use ($out) {
                foreach ($chunk as $order) {
                    fputcsv($out, [
                        $order->id,
                        $order->created_at?->toDateTimeString(),
                        $order->user?->email,
                        $order->user?->name,
                        $order->status,
                        $order->shipping_status,
                        $order->payment_method,
                        $order->total_amount,
                        $order->phone_snapshot,
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $order->load(['user', 'coupon', 'items.product', 'items.productVariant.attributeValues.attribute']);

        $activities = \App\Models\StaffActivity::query()
            ->where('subject_type', Order::class)
            ->where('subject_id', $order->id)
            ->with('user:id,name')
            ->latest()
            ->limit(30)
            ->get();

        return view('staff.orders.show', compact('order', 'activities'));
    }

    public function print(Order $order): View
    {
        $order->load(['user', 'items.product', 'items.productVariant.attributeValues.attribute']);

        return view('staff.orders.print', compact('order'));
    }

    public function pdf(Order $order): Response
    {
        $order->load(['user', 'items.product', 'items.productVariant.attributeValues.attribute']);

        return Pdf::loadView('staff.orders.print', compact('order'))
            ->setPaper('a4')
            ->download('nova-order-'.$order->id.'.pdf');
    }

    public function updateInternalNotes(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'internal_notes' => 'nullable|string|max:20000',
        ]);

        $order->update([
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        StaffActivity::record($request->user(), 'order.internal_notes', Order::class, $order->id, []);

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Đã lưu ghi chú nội bộ.');
    }

    public function resendStatusEmail(Request $request, Order $order): RedirectResponse
    {
        $order->loadMissing([
            'user',
            'coupon',
            'items.product',
            'items.productVariant.attributeValues.attribute',
        ]);
        $user = $order->user;
        $email = $user?->email;
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->route('staff.orders.show', $order)
                ->with('error', 'Khách không có email hợp lệ để gửi.');
        }

        $st = (string) $order->status;
        try {
            Mail::to($email)->send(new OrderStatusChangedMail($order, $st, $st));
        } catch (\Throwable $e) {
            return redirect()
                ->route('staff.orders.show', $order)
                ->with('error', 'Gửi email thất bại: '.$e->getMessage());
        }

        StaffActivity::record($request->user(), 'order.email_resend', Order::class, $order->id, ['type' => 'status_reminder']);

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Đã gửi lại email trạng thái đơn tới khách.');
    }

    public function sendSmsPlaceholder(Request $request, Order $order): RedirectResponse
    {
        if (! config('staff.sms_enabled')) {
            return redirect()
                ->route('staff.orders.show', $order)
                ->with('error', 'SMS chưa bật. Đặt STAFF_SMS_ENABLED=true và tích hợp Twilio (hoặc gateway) trong code khi cần.');
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Đã xếp hàng gửi SMS (demo — cấu hình gateway thật).');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $allowed = [
            Order::STATUS_UNPAID,
            Order::STATUS_PAYMENT_FAILED,
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPING,
            Order::STATUS_AWAITING_DELIVERY,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
            Order::STATUS_RETURN_REFUND,
        ];
        $request->validate([
            'status' => 'required|in:'.implode(',', $allowed),
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $newStatus = (string) $request->input('status');
        $oldStatus = (string) $order->status;

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            if ($newStatus === Order::STATUS_CANCELLED && $oldStatus !== Order::STATUS_CANCELLED) {
                $order->loadMissing('items');
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::query()->where('id', $item->product_variant_id)->lockForUpdate()->first();
                        if ($variant) {
                            $variant->increment('stock', $item->quantity);
                        }
                    } else {
                        $product = Product::query()->where('id', $item->product_id)->lockForUpdate()->first();
                        if ($product) {
                            $product->increment('quantity', $item->quantity);
                        }
                    }

                    InventoryLog::create([
                        'product_variant_id' => $item->product_variant_id,
                        'order_id' => $order->id,
                        'type' => 'import',
                        'quantity' => $item->quantity,
                        'source' => 'staff_cancel',
                        'note' => 'Nhân viên cập nhật hủy đơn, hoàn tồn kho.',
                    ]);
                }
            }

            $order->update([
                'status' => $newStatus,
                'shipping_status' => Order::mapShippingStatusFromOrderStatus($newStatus),
            ]);

            if ($newStatus === Order::STATUS_COMPLETED && $order->payment_method === Order::PAYMENT_METHOD_COD) {
                $order->update(['payment_status' => Order::PAYMENT_STATUS_PAID]);
            }
        });

        if ($oldStatus !== $newStatus) {
            StaffActivity::record($request->user(), 'order.status', Order::class, $order->id, [
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);
        }

        return redirect()
            ->route('staff.orders.show', $order)
            ->with('success', 'Đã cập nhật trạng thái đơn hàng #'.$order->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Order>
     */
    protected function filteredOrdersQuery(Request $request)
    {
        $status = $request->query('status', 'all');
        $shippingStatus = $request->query('shipping_status', 'all');
        $q = trim((string) $request->query('q', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Order::with(['user:id,name,email', 'items.product:id,name,image', 'items.productVariant'])
            ->latest();

        if ($status !== 'all') {
            if ($status === Order::STATUS_UNPAID) {
                $query->pendingPaymentTab();
            } elseif (in_array($status, Order::tabStatusKeys(), true)) {
                $query->where('status', $status);
            }
        }

        if ($shippingStatus !== 'all' && in_array($shippingStatus, Order::tabShippingStatusKeys(), true)) {
            $query->where('shipping_status', $shippingStatus);
        }

        if ($q !== '') {
            $esc = str_replace(['%', '_'], ['\\%', '\\_'], $q);
            $query->where(function ($qb) use ($esc) {
                $qb->where('id', 'like', '%'.$esc.'%')
                    ->orWhere('phone_snapshot', 'like', '%'.$esc.'%')
                    ->orWhere('shipping_address_snapshot', 'like', '%'.$esc.'%')
                    ->orWhereHas('user', function ($uq) use ($esc) {
                        $uq->where('name', 'like', '%'.$esc.'%')
                            ->orWhere('email', 'like', '%'.$esc.'%');
                    });
            });
        }

        if (is_string($dateFrom) && $dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (is_string($dateTo) && $dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query;
    }
}
