<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionReversal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'reversed_by',
        'reason',
    ];

    function transaction()
    {
        return $this->belongsTo(
            Transaction::class
        );
    }

    function user()
    {
        return $this->belongsTo(
            User::class,
            'reversed_by'
        );
    }
}