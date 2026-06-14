<?php

namespace App\Services\Contracts;

use App\Models\Transaction;

interface DepositServiceInterface
{
    public function execute(
        int $walletId,
        float $amount
    ): Transaction;
}