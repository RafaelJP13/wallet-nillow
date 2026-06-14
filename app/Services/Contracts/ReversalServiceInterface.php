<?php

namespace App\Services\Contracts;

use App\Models\Transaction;

interface ReversalServiceInterface
{
    public function execute(
        Transaction $transaction,
        int $reversedBy,
        string $reason
    ): Transaction;
}