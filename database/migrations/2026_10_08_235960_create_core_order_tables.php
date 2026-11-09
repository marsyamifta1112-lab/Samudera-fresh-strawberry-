<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel inti yang sebelumnya dibuat manual. Kolom tambahan (recovery code,
// ongkir, data pengiriman, nomor struk, dst.) ditambahkan oleh migrasi
// berikutnya, jadi tidak dimasukkan di sini.
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pelanggan')) {
            Schema::create('pelanggan', function (Blueprint $table) {
                $table->integer('id_pelanggan')->autoIncrement();
                $table->string('nama_pelanggan', 100)->nullable();
                $table->string('email', 100)->nullable()->unique();
                $table->string('username', 50)->unique();
                $table->string('password', 255);
            });
        }

        if (! Schema::hasTable('petugas')) {
            Schema::create('petugas', function (Blueprint $table) {
                $table->increments('id_petugas');
                $table->string('nama_petugas', 100)->nullable();
                $table->string('username', 50)->unique();
                $table->string('password', 255);
                $table->string('no_telp', 15)->nullable();
                $table->text('alamat')->nullable();
                $table->string('role', 30)->default('Kurir');
            });
        }

        if (! Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table) {
                $table->integer('id_pesanan')->autoIncrement();
                $table->integer('id_pelanggan')->nullable()->index();
                $table->dateTime('tanggal_pemesanan')->nullable();
                $table->decimal('total_harga', 12, 2)->nullable();
                $table->string('status_pesanan', 30)->default('menunggu');
            });
        }

        if (! Schema::hasTable('detail_pesanan')) {
            Schema::create('detail_pesanan', function (Blueprint $table) {
                $table->integer('id_detail')->autoIncrement();
                $table->integer('id_pesanan')->index();
                $table->integer('id_produk')->nullable();
                $table->integer('jumlah')->nullable();
                $table->decimal('harga', 12, 2)->nullable();
                $table->decimal('subtotal', 12, 2)->nullable();
            });
        }

        if (! Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table) {
                $table->integer('id_pembayaran')->autoIncrement();
                $table->integer('id_pesanan')->index();
                $table->string('metode_pembayaran', 50)->nullable();
                $table->decimal('jumlah_bayar', 12, 2)->nullable();
                $table->string('status_pembayaran', 30)->default('menunggu');
                $table->dateTime('tanggal_pembayaran')->nullable();
                $table->string('bukti_pembayaran', 255)->nullable();
            });
        }

        if (! Schema::hasTable('pengiriman')) {
            Schema::create('pengiriman', function (Blueprint $table) {
                $table->integer('id_pengiriman')->autoIncrement();
                $table->integer('id_pesanan')->index();
                $table->unsignedInteger('id_petugas')->nullable()->index();
                $table->string('nama_penerima', 100)->nullable();
                $table->string('no_telp_penerima', 20)->nullable();
                $table->text('alamat_pengiriman')->nullable();
                $table->string('status_pengiriman', 30)->default('menunggu');
                $table->dateTime('tanggal_pengiriman')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Sengaja kosong: tabel ini berisi data bisnis.
    }
};
