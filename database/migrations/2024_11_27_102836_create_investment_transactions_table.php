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
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->decimal('amount', 15, 2); // Transaction amount
            $table->enum('transaction_type', ['investment', 'withdrawal']); // Type of transaction
            $table->timestamp('transaction_date')->useCurrent(); // Date of transaction
            $table->string('notes')->nullable(); // Additional notes
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
