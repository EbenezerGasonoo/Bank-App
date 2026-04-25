<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wire_code_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, issued, rejected
            $table->timestamp('requested_at');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
        });

        // Enforce admin-issued flow by clearing any existing customer wire codes.
        DB::table('users')
            ->where('role', 'user')
            ->update([
                'wire_pin_hash' => null,
                'tax_code_hash' => null,
                'imf_code_hash' => null,
                'cot_code_hash' => null,
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wire_code_requests');
    }
};

