<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fdrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->decimal('principal', 18, 2);
            $table->decimal('annual_rate', 5, 2);
            $table->unsignedInteger('term_months');
            $table->string('payout_mode')->default('maturity'); // maturity, monthly
            $table->decimal('expected_interest', 18, 2);
            $table->decimal('maturity_amount', 18, 2);
            $table->string('status')->default('active'); // active, matured, closed
            $table->dateTime('starts_at');
            $table->dateTime('matures_at');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('matures_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fdrs');
    }
};

