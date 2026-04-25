<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('card_number_masked', 20); // e.g. **** **** **** 4242
            $table->string('card_number_encrypted'); // encrypted full number
            $table->string('expiration', 7); // MM/YYYY
            $table->string('cvv_encrypted'); // encrypted CVV
            $table->string('cardholder_name');
            $table->boolean('is_frozen')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
