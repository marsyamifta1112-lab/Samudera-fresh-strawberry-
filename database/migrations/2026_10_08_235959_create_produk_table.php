<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->integer('id_produk')->autoIncrement();
            $table->string('nama_produk', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_produk', 12, 2)->nullable();
            $table->integer('stok_produk')->nullable();
            $table->string('foto_produk', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};