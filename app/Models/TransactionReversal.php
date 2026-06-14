<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Timestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Timestamps(false)]
#[Fillable([
    'transaction_id',
    'reversed_by',
    'reason',
])]
class TransactionReversal extends Model
{
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reversed_by'
        );
    }
}