<?php

namespace App\Repositories;

use App\Models\TransactionReversal;
use App\Repositories\Contracts\TransactionReversalRepositoryInterface;

class TransactionReversalRepository
    implements TransactionReversalRepositoryInterface
{
    public function create(
        array $data
    ): TransactionReversal {
        return TransactionReversal::create($data);
    }
}