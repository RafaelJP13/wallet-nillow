<?php

namespace App\Services\Contracts;

use App\Models\Transaction;

interface TransactionServiceInterface
{
    public function transfer(
        int $fromWalletId,
        int $toWalletId,
        float $amount
    ): Transaction;

    public function reverse(
        int $transactionId,
        int $reversedBy,
        string $reason
    ): Transaction;
}