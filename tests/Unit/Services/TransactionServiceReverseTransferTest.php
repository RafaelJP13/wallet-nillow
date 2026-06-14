<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceReverseTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_reverse_transfer(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        // usa wallets criadas pelo Observer (evita duplicação e erro UNIQUE)
        $senderWallet = $sender->wallet;
        $receiverWallet = $receiver->wallet;

        // estado inicial controlado
        $senderWallet->update(['balance' => 1000]);
        $receiverWallet->update(['balance' => 0]);

        $service = app(TransactionService::class);

        $transaction = $service->transfer(
            fromWalletId: $senderWallet->id,
            toWalletId: $receiverWallet->id,
            amount: 200
        );

        $service->reverse(
            transactionId: $transaction->id,
            reversedBy: $sender->id,
            reason: 'User request'
        );

        $senderWallet->refresh();
        $receiverWallet->refresh();

        $this->assertEquals(
            1000,
            $senderWallet->balance
        );

        $this->assertEquals(
            0,
            $receiverWallet->balance
        );
    }
}