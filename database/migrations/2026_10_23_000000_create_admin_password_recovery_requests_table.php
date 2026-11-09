<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_password_recovery_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('reviewer_id')->nullable();
            $table->string('token_hash', 64)->nullable();
            $table->string('status', 30);
            $table->dateTime('requested_at');
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->foreign('admin_id')
                ->references('id_petugas')
                ->on('petugas')
                ->cascadeOnDelete();
            $table->foreign('reviewer_id')
                ->references('id_petugas')
                ->on('petugas')
                ->nullOnDelete();
            $table->index(['status', 'expires_at']);
            $table->index(['admin_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_password_recovery_requests');
    }
};
