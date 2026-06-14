<?php

namespace Tests\Unit\Services;

use App\Models\User;
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

        // usa wallet criada pelo Observer (evita duplicação e UNIQUE constraint)
        $wallet = $user->wallet;

        // estado controlado
        $wallet->update(['balance' => 100]);

        $depositService = app(DepositService::class);

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