<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Services\DepositService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceReverseDepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_reverse_deposit(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 100,
        ]);

        $depositService = app(
            DepositService::class
        );

        $transaction = $depositService->execute(
            walletId: $wallet->id,
            amount: 50
        );

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Mistake'
            );

        $wallet->refresh();

        $this->assertEquals(
            100,
            $wallet->balance
        );
    }
}