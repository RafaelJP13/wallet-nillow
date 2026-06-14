<?php

namespace App\Repositories\Contracts;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findById(int $id): ?Wallet;

    public function findByUserId(int $userId): ?Wallet;

}