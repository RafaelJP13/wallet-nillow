<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function sentTransactions()
    {
        return $this->hasMany(
            Transaction::class,
            'from_wallet_id'
        );
    }

    function receivedTransactions()
    {
        return $this->hasMany(
            Transaction::class,
            'to_wallet_id'
        );
    }
}