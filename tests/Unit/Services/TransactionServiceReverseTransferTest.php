<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Wallet;
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

        $senderWallet = Wallet::create([
            'user_id' => $sender->id,
            'balance' => 1000,
        ]);

        $receiverWallet = Wallet::create([
            'user_id' => $receiver->id,
            'balance' => 0,
        ]);

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