<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReviewModeration extends Model
{
    public const ACTION_APPROVE = 'approve';

    public const ACTION_REJECT = 'reject';

    public const ACTION_HIDE = 'hide';

    public const ACTION_UNHIDE = 'unhide';

    protected $fillable = [
        'product_review_id',
        'user_id',
        'action',
        'reason',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
