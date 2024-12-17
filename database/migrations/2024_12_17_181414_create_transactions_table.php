<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdrawal']);
            $table->enum('currency_type', ['crypto', 'fiat'])->default('crypto');
            $table->string('crypto_symbol')->nullable();
            $table->string('fiat_currency')->nullable();
            $table->decimal('amount', 18, 8);
            $table->decimal('fee', 18, 8)->default(0);
            $table->string('tx_id')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed', 'processed'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'cash'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
