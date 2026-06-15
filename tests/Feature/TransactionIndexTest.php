<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_transactions_list(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/transactions');

        $response->assertOk();
    }
}