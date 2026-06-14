<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;

    public function findById(int $id): ?Transaction;

    public function save(Transaction $transaction): bool;

    public function paginateForUser(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator;
}