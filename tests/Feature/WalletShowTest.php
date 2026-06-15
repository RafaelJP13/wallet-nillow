<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_authenticated_user_wallet(): void
    {
        $user = \App\Models\User::factory()->create();

        \Laravel\Sanctum\Sanctum::actingAs($user);

        $this->getJson('/wallet')
            ->assertOk();
    }

    public function test_should_require_authentication(): void
    {
        $this->getJson('/wallet')
            ->assertStatus(302)
            ->assertRedirect('http://localhost/login');
    }
}