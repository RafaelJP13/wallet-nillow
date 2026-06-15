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

    public function findForUpdate(int $id): Wallet
    {
        return Wallet::query()
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function incrementBalance(
        Wallet $wallet,
        float $amount
    ): void {
        $wallet->increment(
            'balance',
            $amount
        );
    }

    public function decrementBalance(
        Wallet $wallet,
        float $amount
    ): void {
        $wallet->decrement(
            'balance',
            $amount
        );
    }

    public function save(
        Wallet $wallet
    ): bool {
        return $wallet->save();
    }
}