<?php

namespace App\Repositories;

use App\Models\TransactionDetail;
use App\Repositories\Contracts\TransactionDetailRepositoryInterface;

class TransactionDetailRepository
    implements TransactionDetailRepositoryInterface
{
    public function create(
        array $data
    ): TransactionDetail {
        return TransactionDetail::create($data);
    }
}