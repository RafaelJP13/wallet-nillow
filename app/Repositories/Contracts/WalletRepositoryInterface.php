<?php

namespace App\Repositories\Contracts;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findById(int $id): ?Wallet;

    public function findByUserId(int $userId): ?Wallet;

    public function findForUpdate(int $id): Wallet;

    public function incrementBalance(
        Wallet $wallet,
        float $amount
    ): void;

    public function save(Wallet $wallet): bool;
}