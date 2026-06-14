<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Timestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Timestamps(false)]
#[Fillable([
    'transaction_id',
    'balance_before',
    'balance_after',
])]
class TransactionDetail extends Model
{
    protected function casts(): array
    {
        return [
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class
        );
    }
}