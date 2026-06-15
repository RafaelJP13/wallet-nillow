<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\Domain\InvalidAmountException;
use App\Exceptions\Domain\WalletNotFoundException;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionDetailRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Services\Contracts\DepositServiceInterface;
use Illuminate\Support\Facades\DB;

class DepositService implements DepositServiceInterface
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionDetailRepositoryInterface $transactionDetailRepository,
    ) {
    }

    public function execute(
        int $walletId,
        float $amount
    ): Transaction {
        return DB::transaction(function () use (
            $walletId,
            $amount
        ) {
            if ($amount <= 0) {
                throw new InvalidAmountException();
            }

            $wallet = $this->walletRepository
                ->findForUpdate($walletId);

            if (! $wallet) {
                throw new WalletNotFoundException();
            }

            $balanceBefore = $wallet->balance;

            $this->walletRepository
                ->incrementBalance(
                    $wallet,
                    $amount
                );

            $wallet->refresh();

            $transaction = $this->transactionRepository
                ->create([
                    'to_wallet_id' => $wallet->id,
                    'type' => TransactionType::DEPOSIT,
                    'amount' => $amount,
                    'status' => TransactionStatus::COMPLETED,
                    'description' => TransactionType::DEPOSIT->value,
                ]);

            $this->transactionDetailRepository
                ->create([
                    'transaction_id' => $transaction->id,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                ]);

            return $transaction->fresh([
                'detail',
                'toWallet',
            ]);
        });
    }
}