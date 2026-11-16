<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pengaturan_operasional')) {
            Schema::create('pengaturan_operasional', function (Blueprint $table): void {
                $table->unsignedTinyInteger('id')->primary();
                $table->time('jam_buka');
                $table->time('jam_tutup');
                $table->json('hari_operasional');
                $table->json('tanggal_libur');
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (! DB::table('pengaturan_operasional')->where('id', 1)->exists()) {
            DB::table('pengaturan_operasional')->insert([
                'id' => 1,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '17:00:00',
                'hari_operasional' => json_encode([1, 2, 3, 4, 5, 6], JSON_THROW_ON_ERROR),
                'tanggal_libur' => json_encode([], JSON_THROW_ON_ERROR),
            ]);
        }

        if (Schema::hasTable('pesanan') && ! Schema::hasColumn('pesanan', 'tanggal_pemrosesan')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->date('tanggal_pemrosesan')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanan') && Schema::hasColumn('pesanan', 'tanggal_pemrosesan')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->dropColumn('tanggal_pemrosesan');
            });
        }

        Schema::dropIfExists('pengaturan_operasional');
    }
};
