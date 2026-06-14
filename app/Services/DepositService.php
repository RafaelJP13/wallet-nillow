<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Wallet;
use App\Services\Contracts\DepositServiceInterface;
use Illuminate\Support\Facades\DB;

class DepositService implements DepositServiceInterface
{
    public function execute(
        int $walletId,
        float $amount
    ): Transaction {
        return DB::transaction(function () use (
            $walletId,
            $amount
        ) {
            $wallet = Wallet::query()->findOrFail($walletId);

            $balanceBefore = $wallet->balance;

            $wallet->increment('balance', $amount);

            $wallet->refresh();

            $transaction = Transaction::create([
                'to_wallet_id' => $wallet->id,
                'type' => TransactionType::DEPOSIT,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => 'Deposit',
            ]);

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
            ]);

            return $transaction;
        });
    }
}