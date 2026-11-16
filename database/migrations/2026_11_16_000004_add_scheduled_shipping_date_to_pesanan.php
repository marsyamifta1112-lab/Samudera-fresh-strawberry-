<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanan') && ! Schema::hasColumn('pesanan', 'jadwal_pengiriman')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->date('jadwal_pengiriman')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanan') && Schema::hasColumn('pesanan', 'jadwal_pengiriman')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->dropColumn('jadwal_pengiriman');
            });
        }
    }
};
