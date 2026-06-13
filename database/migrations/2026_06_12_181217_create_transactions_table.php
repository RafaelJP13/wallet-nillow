<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_wallet_id')
                ->nullable()
                ->constrained('wallets')
                ->nullOnDelete();

            $table->foreignId('to_wallet_id')
                ->nullable()
                ->constrained('wallets')
                ->nullOnDelete();

            $table->enum(
                'type',
                array_column(TransactionType::cases(), 'value')
            );

            $table->decimal('amount', 15, 2);

            $table->enum(
                'status',
                array_column(TransactionStatus::cases(), 'value')
            );

            $table->string('description')
                ->nullable();

            $table->timestamps();

            $table->index('from_wallet_id');
            $table->index('to_wallet_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};