<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionDetailRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransactionReversalRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Services\Contracts\TransactionServiceInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionDetailRepositoryInterface $transactionDetailRepository,
        private readonly TransactionReversalRepositoryInterface $transactionReversalRepository,
    ) {
    }

    public function transfer(
        int $fromWalletId,
        int $toWalletId,
        float $amount
    ): Transaction {
        return DB::transaction(function () use (
            $fromWalletId,
            $toWalletId,
            $amount
        ) {
            $fromWallet = $this->walletRepository
                ->findForUpdate($fromWalletId);

            $toWallet = $this->walletRepository
                ->findForUpdate($toWalletId);

            if ($fromWallet->balance < $amount) {
                throw new DomainException(
                    'Saldo Insuficiente para realizar a transferência.'
                );
            }

            $balanceBefore = $fromWallet->balance;

            $this->walletRepository
                ->decrementBalance(
                    $fromWallet,
                    $amount
                );

            $this->walletRepository
                ->incrementBalance(
                    $toWallet,
                    $amount
                );

            $fromWallet->refresh();

            $transaction = $this->transactionRepository
                ->create([
                    'from_wallet_id' => $fromWallet->id,
                    'to_wallet_id' => $toWallet->id,
                    'type' => TransactionType::TRANSFER,
                    'amount' => $amount,
                    'status' => TransactionStatus::COMPLETED,
                    'description' => 'Transfer',
                ]);

            $this->transactionDetailRepository
                ->create([
                    'transaction_id' => $transaction->id,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $fromWallet->balance,
                ]);

            return $transaction;
        });
    }

    public function reverse(
        int $transactionId,
        int $reversedBy,
        string $reason
    ): Transaction {
        return DB::transaction(function () use (
            $transactionId,
            $reversedBy,
            $reason
        ) {
            $transaction = $this->transactionRepository
                ->findByIdWithReversal($transactionId);

            if (! $transaction) {
                throw new DomainException(
                    'Transaction not found.'
                );
            }

            if ($transaction->reversal) {
                throw new DomainException(
                    'Transação já foi revertida.'
                );
            }

            $receiverWallet = $this->walletRepository
                ->findForUpdate(
                    $transaction->to_wallet_id
                );

            if ($receiverWallet->user_id !== $reversedBy) {
                throw new DomainException(
                    'Apenas o destinatário da transação pode fazer a reversão.'
                );
            }

            if ($receiverWallet->balance < $transaction->amount) {
                throw new DomainException(
                    'Saldo recebido insuficiente para reverter esta transação.'
                );
            }

            if (
                $transaction->type === TransactionType::DEPOSIT
            ) {
                $this->walletRepository
                    ->decrementBalance(
                        $receiverWallet,
                        $transaction->amount
                    );
            }

            if (
                $transaction->type === TransactionType::TRANSFER
            ) {
                $fromWallet = $this->walletRepository
                    ->findForUpdate(
                        $transaction->from_wallet_id
                    );

                $this->walletRepository
                    ->decrementBalance(
                        $receiverWallet,
                        $transaction->amount
                    );

                $this->walletRepository
                    ->incrementBalance(
                        $fromWallet,
                        $transaction->amount
                    );
            }

            $this->transactionReversalRepository
                ->create([
                    'transaction_id' => $transaction->id,
                    'reversed_by' => $reversedBy,
                    'reason' => $reason,
                ]);

            $this->transactionRepository
                ->updateStatus(
                    $transaction,
                    TransactionStatus::REVERSED
                );

            return $transaction->fresh([
                'detail',
                'reversal',
                'fromWallet',
                'toWallet',
            ]);
        });
    }
}