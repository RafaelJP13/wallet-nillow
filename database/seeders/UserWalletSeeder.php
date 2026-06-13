<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserWalletSeeder extends Seeder
{
    function run(): void
    {
        $user = User::create([
            'name' => 'Rafael Santos Fernanandes',
            'email' => 'rafael@example.com',
            'password' => Hash::make('password'),
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'balance' => 1000.00,
        ]);
    }
}