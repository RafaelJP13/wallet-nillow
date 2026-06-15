<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionStatus;
use App\Models\User;
use App\Services\DepositService;
use App\Services\TransactionService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                reason: 'Erro'
            );

        $wallet->refresh();

        $this->assertEquals(
            100,
            $wallet->balance
        );
    }

    public function test_should_create_reversal_record(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Erro'
            );

        $this->assertDatabaseHas('transaction_reversals', [
            'transaction_id' => $transaction->id,
            'reversed_by' => $user->id,
            'reason' => 'Erro',
        ]);
    }

    public function test_should_mark_transaction_as_reversed(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Erro'
            );

        $transaction->refresh();

        $this->assertEquals(
            TransactionStatus::REVERSED,
            $transaction->status
        );
    }

    public function test_should_not_reverse_same_transaction_twice(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Erro'
            );

        $this->expectException(DomainException::class);

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Erro'
            );
    }

    public function test_should_not_reverse_deposit_when_user_is_not_receiver(): void
    {
        $receiver = User::factory()->create();
        $otherUser = User::factory()->create();

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $receiver->wallet->id,
                amount: 100
            );

        $this->expectException(DomainException::class);

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $otherUser->id,
                reason: 'Erro'
            );
    }

    public function test_should_throw_exception_when_transaction_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(TransactionService::class)
            ->reverse(
                transactionId: 999999,
                reversedBy: 1,
                reason: 'Erro'
            );
    }

    public function test_should_restore_original_balance_after_reversal(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $wallet->update([
            'balance' => 500
        ]);

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 250
            );

        $wallet->refresh();

        $this->assertEquals(
            750,
            $wallet->balance
        );

        app(TransactionService::class)
            ->reverse(
                transactionId: $transaction->id,
                reversedBy: $user->id,
                reason: 'Erro'
            );

        $wallet->refresh();

        $this->assertEquals(
            500,
            $wallet->balance
        );
    }
}
