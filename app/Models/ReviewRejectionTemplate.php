<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRejectionTemplate extends Model
{
    protected $fillable = [
        'label',
        'body',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
