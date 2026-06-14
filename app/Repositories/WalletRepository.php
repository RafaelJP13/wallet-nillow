<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface
{
    public function findById(int $id): ?Wallet
    {
        return Wallet::query()->find($id);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function save(Wallet $wallet): bool
    {
        return $wallet->save();
    }
}