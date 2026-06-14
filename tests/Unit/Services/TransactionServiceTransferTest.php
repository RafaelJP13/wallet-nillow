<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_transfer_money_between_wallets(): void
    {
        $sender = User::factory()->create();

        $receiver = User::factory()->create();

        $senderWallet = Wallet::create([
            'user_id' => $sender->id,
            'balance' => 500,
        ]);

        $receiverWallet = Wallet::create([
            'user_id' => $receiver->id,
            'balance' => 100,
        ]);

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

        $senderWallet = Wallet::create([
            'user_id' => $sender->id,
            'balance' => 50,
        ]);

        $receiverWallet = Wallet::create([
            'user_id' => $receiver->id,
            'balance' => 0,
        ]);

        $service = app(TransactionService::class);

        $service->transfer(
            fromWalletId: $senderWallet->id,
            toWalletId: $receiverWallet->id,
            amount: 100
        );
    }
}