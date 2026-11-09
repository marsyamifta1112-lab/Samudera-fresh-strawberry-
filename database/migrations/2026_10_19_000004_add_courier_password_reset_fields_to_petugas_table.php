<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dateTime('password_reset_requested_at')->nullable();
            $table->string('password_reset_status', 30)->nullable();
            $table->boolean('must_change_password')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn([
                'password_reset_requested_at',
                'password_reset_status',
                'must_change_password',
            ]);
        });
    }
};
