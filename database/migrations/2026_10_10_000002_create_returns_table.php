<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->increments('id_return');
            $table->integer('id_detail');
            $table->integer('id_pesanan');
            $table->integer('id_pelanggan');
            $table->integer('id_produk');
            $table->unsignedInteger('jumlah');
            $table->decimal('jumlah_pengembalian', 12, 2);
            $table->text('alasan');
            $table->string('foto_bukti', 255);
            $table->string('bank_pengembalian', 30);
            $table->string('nama_pemilik_rekening', 100);
            $table->string('nomor_rekening', 34);
            $table->string('status_return', 40)->default('Diajukan');
            $table->text('catatan_admin')->nullable();
            $table->string('status_pengembalian_dana', 30)->default('Menunggu');
            $table->string('referensi_transfer', 100)->nullable();
            $table->timestamp('stok_dikembalikan_at')->nullable();
            $table->timestamp('dana_ditransfer_at')->nullable();
            $table->timestamps();

            $table->foreign('id_detail')->references('id_detail')->on('detail_pesanan')->restrictOnDelete();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->restrictOnDelete();
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->restrictOnDelete();
            $table->foreign('id_produk')->references('id_produk')->on('produk')->restrictOnDelete();
            $table->index(['id_pesanan', 'id_detail', 'status_return'], 'returns_order_detail_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
