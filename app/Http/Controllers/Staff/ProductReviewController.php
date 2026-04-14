<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\ProductReviewRejectedMail;
use App\Models\ProductReview;
use App\Models\ProductReviewModeration;
use App\Models\ReviewRejectionTemplate;
use App\Models\StaffActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = ProductReview::query()
            ->where('is_approved', false)
            ->with(['user', 'product', 'images'])
            ->latest()
            ->paginate(20);

        $rejectionTemplates = ReviewRejectionTemplate::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('staff.product_reviews.index', compact('reviews', 'rejectionTemplates'));
    }

    public function published(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $query = ProductReview::query()
            ->where('is_approved', true)
            ->with([
                'user:id,name,email',
                'product:id,name',
                'images',
                'hiddenByUser:id,name',
                'moderations' => fn ($q) => $q->with('user:id,name')->latest()->limit(25),
            ]);

        if ($q !== '') {
            $esc = str_replace(['%', '_'], ['\\%', '\\_'], $q);
            $query->where(function ($qb) use ($esc) {
                $qb->where('id', 'like', '%'.$esc.'%')
                    ->orWhere('title', 'like', '%'.$esc.'%')
                    ->orWhere('content', 'like', '%'.$esc.'%')
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', '%'.$esc.'%'))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$esc.'%')
                        ->orWhere('email', 'like', '%'.$esc.'%'));
            });
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('staff.product_reviews.published', compact('reviews', 'q'));
    }

    public function approve(Request $request, ProductReview $review): RedirectResponse
    {
        DB::transaction(function () use ($review, $request) {
            $review->is_approved = true;
            $review->approved_at = now();
            $review->rejected_at = null;
            $review->rejection_reason = null;
            $review->is_hidden = false;
            $review->hidden_at = null;
            $review->hidden_by = null;
            $review->hidden_reason = null;
            $review->save();

            ProductReviewModeration::query()->create([
                'product_review_id' => $review->id,
                'user_id' => $request->user()?->id,
                'action' => ProductReviewModeration::ACTION_APPROVE,
                'reason' => null,
            ]);
        });

        StaffActivity::record($request->user(), 'review.approve', ProductReview::class, $review->id, []);

        return redirect()->route('staff.product-reviews.index')->with('success', 'Đã duyệt đánh giá.');
    }

    public function reject(Request $request, ProductReview $review): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $review->loadMissing(['user', 'product']);

        DB::transaction(function () use ($review, $validated, $request) {
            $review->is_approved = false;
            $review->approved_at = null;
            $review->rejected_at = now();
            $review->rejection_reason = $validated['reason'] ?? null;
            $review->save();

            ProductReviewModeration::query()->create([
                'product_review_id' => $review->id,
                'user_id' => $request->user()?->id,
                'action' => ProductReviewModeration::ACTION_REJECT,
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        StaffActivity::record($request->user(), 'review.reject', ProductReview::class, $review->id, [
            'reason' => $validated['reason'] ?? null,
        ]);

        try {
            if ($review->user?->email) {
                Mail::to($review->user->email)->send(new ProductReviewRejectedMail($review));
            }
        } catch (\Throwable $e) {
            Log::warning('Send ProductReviewRejectedMail failed', [
                'review_id' => $review->id,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('staff.product-reviews.index')->with('success', 'Đã từ chối đánh giá.');
    }

    public function hide(Request $request, ProductReview $review): RedirectResponse
    {
        if (! $review->is_approved) {
            return redirect()->back()->with('error', 'Chỉ ẩn được đánh giá đã duyệt (đang hiển thị công khai).');
        }

        $validated = $request->validate([
            'hidden_reason' => 'nullable|string|max:500',
        ]);

        $review->is_hidden = true;
        $review->hidden_at = now();
        $review->hidden_by = $request->user()?->id;
        $review->hidden_reason = $validated['hidden_reason'] ?? null;
        $review->save();

        ProductReviewModeration::query()->create([
            'product_review_id' => $review->id,
            'user_id' => $request->user()?->id,
            'action' => ProductReviewModeration::ACTION_HIDE,
            'reason' => $validated['hidden_reason'] ?? null,
        ]);

        StaffActivity::record($request->user(), 'review.hide', ProductReview::class, $review->id, []);

        return redirect()->route('staff.product-reviews.published')->with('success', 'Đã ẩn đánh giá khỏi cửa hàng.');
    }

    public function unhide(Request $request, ProductReview $review): RedirectResponse
    {
        $review->is_hidden = false;
        $review->hidden_at = null;
        $review->hidden_by = null;
        $review->hidden_reason = null;
        $review->save();

        ProductReviewModeration::query()->create([
            'product_review_id' => $review->id,
            'user_id' => $request->user()?->id,
            'action' => ProductReviewModeration::ACTION_UNHIDE,
            'reason' => null,
        ]);

        StaffActivity::record($request->user(), 'review.unhide', ProductReview::class, $review->id, []);

        return redirect()->route('staff.product-reviews.published')->with('success', 'Đã hiển thị lại đánh giá trên cửa hàng.');
    }
}
