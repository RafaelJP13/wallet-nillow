<?php

namespace App\Repositories\Contracts;

use App\Models\TransactionDetail;

interface TransactionDetailRepositoryInterface
{
    public function create(
        array $data
    ): TransactionDetail;
}