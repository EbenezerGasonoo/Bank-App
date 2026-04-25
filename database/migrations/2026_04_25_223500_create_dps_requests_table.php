<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dps_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('monthly_amount', 15, 2);
            $table->unsignedInteger('tenure_months');
            $table->decimal('interest_rate', 5, 2)->default(6.50);
            $table->enum('status', ['pending', 'active', 'closed', 'rejected'])->default('pending');
            $table->string('plan_name', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dps_requests');
    }
};
