<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('balance_before', 15, 2);

            $table->decimal('balance_after', 15, 2);

            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};