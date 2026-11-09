<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->bigIncrements('id_keranjang');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedInteger('jumlah');
            $table->unique(['id_pelanggan', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
