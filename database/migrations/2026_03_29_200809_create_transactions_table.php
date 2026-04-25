<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('receiver_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('amount', 18, 2);
            $table->enum('type', ['deposit', 'withdrawal', 'transfer'])->default('transfer');
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'flagged'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
