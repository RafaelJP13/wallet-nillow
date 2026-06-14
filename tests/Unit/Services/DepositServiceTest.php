<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_deposit_money(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;
        $wallet->update(['balance' => 100]);

        $service = app(DepositService::class);

        $transaction = $service->execute(
            walletId: $wallet->id,
            amount: 50
        );

        $wallet->refresh();

        $this->assertInstanceOf(
            Transaction::class,
            $transaction
        );

        $this->assertEquals(
            150,
            $wallet->balance
        );
    }

    public function test_should_create_transaction_record(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'to_wallet_id' => $wallet->id,
            'type' => TransactionType::DEPOSIT,
            'amount' => 100,
            'status' => TransactionStatus::COMPLETED,
            'description' => 'Deposit',
        ]);

        $this->assertEquals(
            $wallet->id,
            $transaction->to_wallet_id
        );

        $this->assertEquals(
            TransactionType::DEPOSIT,
            $transaction->type
        );

        $this->assertEquals(
            TransactionStatus::COMPLETED,
            $transaction->status
        );

        $this->assertEquals(
            'Deposit',
            $transaction->description
        );
    }

    public function test_should_create_transaction_detail(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;
        $wallet->update(['balance' => 100]);

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 50
            );

        $detail = TransactionDetail::query()
            ->where(
                'transaction_id',
                $transaction->id
            )
            ->first();

        $this->assertNotNull($detail);

        $this->assertEquals(
            100,
            $detail->balance_before
        );

        $this->assertEquals(
            150,
            $detail->balance_after
        );

        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'balance_before' => 100,
            'balance_after' => 150,
        ]);
    }

    public function test_should_deposit_into_negative_balance(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;
        $wallet->update(['balance' => -50]);

        app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        $wallet->refresh();

        $this->assertEquals(
            50,
            $wallet->balance
        );
    }

    public function test_should_deposit_decimal_amount(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;
        $wallet->update(['balance' => 100]);

        app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 25.50
            );

        $wallet->refresh();

        $this->assertEquals(
            125.50,
            (float) $wallet->balance
        );
    }

    public function test_should_throw_exception_when_wallet_not_found(): void
    {
        $this->expectException(
            ModelNotFoundException::class
        );

        app(DepositService::class)
            ->execute(
                walletId: 999999,
                amount: 100
            );
    }

    public function test_should_return_created_transaction(): void
    {
        $user = User::factory()->create();

        $wallet = $user->wallet;

        $transaction = app(DepositService::class)
            ->execute(
                walletId: $wallet->id,
                amount: 100
            );

        $this->assertInstanceOf(
            Transaction::class,
            $transaction
        );

        $this->assertEquals(
            TransactionType::DEPOSIT,
            $transaction->type
        );

        $this->assertEquals(
            TransactionStatus::COMPLETED,
            $transaction->status
        );
    }
}