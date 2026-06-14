<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::query()
            ->with([
                'detail',
                'reversal',
                'fromWallet',
                'toWallet',
            ])
            ->find($id);
    }

    public function save(Transaction $transaction): bool
    {
        return $transaction->save();
    }

    public function paginateForUser(
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Transaction::query()
            ->whereHas(
                'fromWallet',
                fn ($query) => $query->where(
                    'user_id',
                    $userId
                )
            )
            ->orWhereHas(
                'toWallet',
                fn ($query) => $query->where(
                    'user_id',
                    $userId
                )
            )
            ->latest()
            ->paginate($perPage);
    }
}