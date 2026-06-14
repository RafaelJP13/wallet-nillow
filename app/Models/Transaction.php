<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Timestamps(false)]
#[Fillable([
    'from_wallet_id',
    'to_wallet_id',
    'type',
    'amount',
    'status',
    'description',
])]
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
        ];
    }

    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(
            Wallet::class,
            'from_wallet_id'
        );
    }

    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(
            Wallet::class,
            'to_wallet_id'
        );
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(
            TransactionReversal::class
        );
    }

    public function detail(): HasOne
    {
        return $this->hasOne(
            TransactionDetail::class
        );
    }
}