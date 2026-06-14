<?php

namespace App\Providers;

use App\Repositories\Contracts\TransactionDetailRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransactionReversalRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Repositories\TransactionDetailRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\TransactionReversalRepository;
use App\Repositories\WalletRepository;
use App\Services\Contracts\DepositServiceInterface;
use App\Services\Contracts\TransactionServiceInterface;
use App\Services\DepositService;
use App\Services\TransactionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            WalletRepositoryInterface::class,
            WalletRepository::class
        );

        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );

        $this->app->bind(
            TransactionDetailRepositoryInterface::class,
            TransactionDetailRepository::class
        );

        $this->app->bind(
            TransactionReversalRepositoryInterface::class,
            TransactionReversalRepository::class
        );

        $this->app->bind(
            DepositServiceInterface::class,
            DepositService::class
        );

        $this->app->bind(
            TransactionServiceInterface::class,
            TransactionService::class
        );
    }
}