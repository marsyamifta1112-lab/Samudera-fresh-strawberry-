<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ongkir_wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah', 120);
            $table->unsignedBigInteger('tarif');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('wilayah_ongkir', 120)->nullable();
        });

        $now = now();
        DB::table('ongkir_wilayah')->insert([
            ['nama_wilayah' => 'Ciwidey & Rancabali', 'tarif' => 10000, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama_wilayah' => 'Pasirjambu & Soreang', 'tarif' => 15000, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama_wilayah' => 'Bandung Raya (Bandung, Cimahi, Bandung Barat)', 'tarif' => 25000, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama_wilayah' => 'Luar Bandung Raya', 'tarif' => 50000, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('wilayah_ongkir');
        });

        Schema::dropIfExists('ongkir_wilayah');
    }
};
