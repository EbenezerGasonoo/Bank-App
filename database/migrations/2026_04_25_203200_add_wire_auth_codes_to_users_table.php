<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wire_pin_hash')->nullable()->after('login_otp_expires_at');
            $table->string('tax_code_hash')->nullable()->after('wire_pin_hash');
            $table->string('imf_code_hash')->nullable()->after('tax_code_hash');
            $table->string('cot_code_hash')->nullable()->after('imf_code_hash');
        });

        // Demo defaults for existing customer users so the feature is immediately usable.
        DB::table('users')
            ->where('role', 'user')
            ->update([
                'wire_pin_hash' => Hash::make('1234'),
                'tax_code_hash' => Hash::make('TAX123'),
                'imf_code_hash' => Hash::make('IMF123'),
                'cot_code_hash' => Hash::make('COT123'),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wire_pin_hash', 'tax_code_hash', 'imf_code_hash', 'cot_code_hash']);
        });
    }
};

