<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_transaction_details(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::create([
            'from_wallet_id' => $user->wallet->id,
            'to_wallet_id' => $receiver->wallet->id,
            'type' => TransactionType::TRANSFER,
            'amount' => 10,
            'status' => TransactionStatus::COMPLETED,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/transactions/{$transaction->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id);
    }
}