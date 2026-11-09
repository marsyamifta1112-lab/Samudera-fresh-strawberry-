<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('nama_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'nama_penerima',
                'alamat_pengiriman',
                'no_hp',
                'catatan',
            ]);
        });
    }
};