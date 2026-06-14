<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionReversal;
use App\Models\Wallet;
use App\Services\Contracts\TransactionServiceInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransactionService implements TransactionServiceInterface
{
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
            $fromWallet = Wallet::query()
                ->lockForUpdate()
                ->findOrFail($fromWalletId);

            $toWallet = Wallet::query()
                ->lockForUpdate()
                ->findOrFail($toWalletId);

            if ($fromWallet->balance < $amount) {
                throw new DomainException(
                    'Insufficient balance.'
                );
            }

            $balanceBefore = $fromWallet->balance;

            $fromWallet->decrement(
                'balance',
                $amount
            );

            $toWallet->increment(
                'balance',
                $amount
            );

            $fromWallet->refresh();

            $transaction = Transaction::create([
                'from_wallet_id' => $fromWallet->id,
                'to_wallet_id' => $toWallet->id,
                'type' => TransactionType::TRANSFER,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => 'Transfer',
            ]);

            TransactionDetail::create([
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
            $transaction = Transaction::query()
                ->with('reversal')
                ->findOrFail($transactionId);

            if ($transaction->reversal) {
                throw new DomainException(
                    'Transaction already reversed.'
                );
            }

            if (
                $transaction->type === TransactionType::DEPOSIT
            ) {
                $wallet = Wallet::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $transaction->to_wallet_id
                    );

                $wallet->decrement(
                    'balance',
                    $transaction->amount
                );
            }

            if (
                $transaction->type === TransactionType::TRANSFER
            ) {
                $fromWallet = Wallet::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $transaction->from_wallet_id
                    );

                $toWallet = Wallet::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $transaction->to_wallet_id
                    );

                $toWallet->decrement(
                    'balance',
                    $transaction->amount
                );

                $fromWallet->increment(
                    'balance',
                    $transaction->amount
                );
            }

            TransactionReversal::create([
                'transaction_id' => $transaction->id,
                'reversed_by' => $reversedBy,
                'reason' => $reason,
            ]);

            $transaction->update([
                'status' => TransactionStatus::REVERSED,
            ]);

            return $transaction->fresh();
        });
    }
}