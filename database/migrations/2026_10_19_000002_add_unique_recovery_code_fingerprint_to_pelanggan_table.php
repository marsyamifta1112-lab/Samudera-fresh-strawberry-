<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('recovery_code_fingerprint', 64)
                ->nullable()
                ->unique('pelanggan_recovery_code_fingerprint_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropUnique('pelanggan_recovery_code_fingerprint_unique');
            $table->dropColumn('recovery_code_fingerprint');
        });
    }
};
