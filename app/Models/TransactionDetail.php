<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'balance_before',
        'balance_after',
    ];

    protected function casts(): array
    {
        return [
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    function transaction()
    {
        return $this->belongsTo(
            Transaction::class
        );
    }
}