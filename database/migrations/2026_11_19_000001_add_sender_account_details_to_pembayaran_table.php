<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('nama_pemilik_rekening_pengirim', 100)->nullable();
            $table->string('nomor_rekening_pengirim', 34)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['nama_pemilik_rekening_pengirim', 'nomor_rekening_pengirim']);
        });
    }
};
