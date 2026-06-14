<?php

namespace App\Repositories\Contracts;

use App\Models\TransactionReversal;

interface TransactionReversalRepositoryInterface
{
    public function create(
        array $data
    ): TransactionReversal;
}