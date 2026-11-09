<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->string('recovery_code_hash')->nullable();
            $table->timestamp('recovery_code_expires_at')->nullable();
            $table->timestamp('recovery_code_used_at')->nullable();
            $table->unsignedTinyInteger('recovery_code_attempts')->default(0);
            $table->timestamp('recovery_code_locked_until')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn([
                'recovery_code_hash',
                'recovery_code_expires_at',
                'recovery_code_used_at',
                'recovery_code_attempts',
                'recovery_code_locked_until',
            ]);
        });
    }
};