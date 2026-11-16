<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran') || ! Schema::hasColumn('pembayaran', 'metode_pembayaran')) {
            throw new RuntimeException('Kolom pembayaran.metode_pembayaran tidak ditemukan.');
        }

        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->string('metode_pembayaran', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pembayaran') || ! Schema::hasColumn('pembayaran', 'metode_pembayaran')) {
            throw new RuntimeException('Kolom pembayaran.metode_pembayaran tidak ditemukan.');
        }

        $hasUnsupportedMethod = DB::table('pembayaran')
            ->whereNotNull('metode_pembayaran')
            ->whereNotIn('metode_pembayaran', ['BCA', 'BRI', 'BNI', 'Mandiri'])
            ->exists();

        if ($hasUnsupportedMethod) {
            throw new RuntimeException('Kolom metode_pembayaran tidak dapat dipersempit karena ada metode pembayaran non-bank.');
        }

        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['BCA', 'BRI', 'BNI', 'Mandiri'])->nullable()->change();
        });
    }
};
