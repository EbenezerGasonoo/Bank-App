<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_number', 20)->unique();
            $table->enum('type', ['checking', 'savings'])->default('checking');
            $table->decimal('balance', 18, 2)->default(0.00);
            $table->enum('status', ['active', 'frozen', 'closed'])->default('active');
            $table->string('currency', 3)->default('GBP');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
