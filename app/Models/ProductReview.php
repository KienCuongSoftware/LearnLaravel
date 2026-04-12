<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'content',
        'variant_classification',
        'is_verified',
        'is_approved',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'is_hidden',
        'hidden_at',
        'hidden_by',
        'hidden_reason',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_hidden' => 'boolean',
        'hidden_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ProductReviewImage::class)->orderBy('sort');
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(ProductReviewModeration::class, 'product_review_id')->latest();
    }

    public function hiddenByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    /** Đánh giá hiển thị trên storefront (đã duyệt và không bị ẩn). */
    public function scopePublicVisible(Builder $query): Builder
    {
        return $query->where('is_approved', true)->where('is_hidden', false);
    }
}
