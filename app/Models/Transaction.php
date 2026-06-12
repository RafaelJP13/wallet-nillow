<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'type',
        'amount',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
        ];
    }
    function fromWallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'from_wallet_id'
        );
    }

    function toWallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'to_wallet_id'
        );
    }

    function reversal()
    {
        return $this->hasOne(
            TransactionReversal::class
        );
    }

    function detail()
    {
        return $this->hasOne(
            TransactionDetail::class
        );
    }
}