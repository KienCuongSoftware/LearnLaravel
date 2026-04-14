<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StaffActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryAdjustmentController extends Controller
{
    public function create(): View
    {
        return view('staff.inventory.adjust');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => 'required|in:variant,product',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'product_id' => 'nullable|integer|exists:products,id',
            'type' => 'required|in:import,export',
            'quantity' => 'required|integer|min:1|max:999999',
            'note' => 'nullable|string|max:2000',
        ], [
            'target_type.required' => 'Chọn loại mục tiêu (biến thể hoặc sản phẩm đơn).',
            'quantity.required' => 'Nhập số lượng.',
        ]);

        if ($validated['target_type'] === 'variant' && empty($validated['product_variant_id'])) {
            return back()->withInput()->withErrors(['product_variant_id' => 'Chọn hoặc nhập ID biến thể.']);
        }
        if ($validated['target_type'] === 'product' && empty($validated['product_id'])) {
            return back()->withInput()->withErrors(['product_id' => 'Chọn hoặc nhập ID sản phẩm (không biến thể).']);
        }

        $qty = (int) $validated['quantity'];
        $type = $validated['type'];
        $note = $validated['note'] ?? 'Điều chỉnh thủ công (nhân viên).';

        try {
            DB::transaction(function () use ($validated, $qty, $type, $note) {
                if ($validated['target_type'] === 'variant') {
                    $variant = ProductVariant::query()->lockForUpdate()->findOrFail((int) $validated['product_variant_id']);
                    if ($type === 'export') {
                        if ($variant->stock < $qty) {
                            throw new \RuntimeException('Tồn kho biến thể không đủ (hiện: '.$variant->stock.').');
                        }
                        $variant->decrement('stock', $qty);
                    } else {
                        $variant->increment('stock', $qty);
                    }
                    InventoryLog::create([
                        'product_variant_id' => $variant->id,
                        'order_id' => null,
                        'type' => $type,
                        'quantity' => $qty,
                        'source' => 'staff_manual',
                        'note' => $note,
                    ]);
                } else {
                    $product = Product::query()->lockForUpdate()->findOrFail((int) $validated['product_id']);
                    if ($type === 'export') {
                        if ($product->quantity < $qty) {
                            throw new \RuntimeException('Tồn kho sản phẩm không đủ (hiện: '.$product->quantity.').');
                        }
                        $product->decrement('quantity', $qty);
                    } else {
                        $product->increment('quantity', $qty);
                    }
                    InventoryLog::create([
                        'product_variant_id' => null,
                        'order_id' => null,
                        'type' => $type,
                        'quantity' => $qty,
                        'source' => 'staff_manual',
                        'note' => $note.' [product_id:'.$product->id.']',
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        StaffActivity::record($request->user(), 'inventory.manual_adjust', null, null, [
            'target_type' => $validated['target_type'],
            'product_variant_id' => $validated['product_variant_id'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'type' => $type,
            'quantity' => $qty,
        ]);

        return redirect()
            ->route('staff.inventory.adjust')
            ->with('success', 'Đã ghi nhận nhập/xuất kho thủ công.');
    }
}
