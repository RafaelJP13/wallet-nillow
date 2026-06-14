<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_transfer_money_between_wallets(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // usa wallet criada automaticamente pelo Observer
        $senderWallet = $sender->wallet;
        $receiverWallet = $receiver->wallet;

        // estado controlado do teste
        $senderWallet->update(['balance' => 500]);
        $receiverWallet->update(['balance' => 100]);

        $service = app(TransactionService::class);

        $transaction = $service->transfer(
            fromWalletId: $senderWallet->id,
            toWalletId: $receiverWallet->id,
            amount: 200
        );

        $senderWallet->refresh();
        $receiverWallet->refresh();

        $this->assertInstanceOf(
            Transaction::class,
            $transaction
        );

        $this->assertEquals(
            300,
            $senderWallet->balance
        );

        $this->assertEquals(
            300,
            $receiverWallet->balance
        );
    }

    public function test_should_not_transfer_when_balance_is_insufficient(): void
    {
        $this->expectException(
            \DomainException::class
        );

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // usa wallet do Observer (sem duplicar insert)
        $senderWallet = $sender->wallet;
        $receiverWallet = $receiver->wallet;

        // estado controlado
        $senderWallet->update(['balance' => 50]);
        $receiverWallet->update(['balance' => 0]);

        /** @var TransactionService $service */
        $service = app(TransactionService::class);

        $service->transfer(
            fromWalletId: $senderWallet->id,
            toWalletId: $receiverWallet->id,
            amount: 100
        );
    }
}