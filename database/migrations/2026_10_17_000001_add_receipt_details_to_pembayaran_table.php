<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('nomor_struk', 32)->nullable()->unique();
            $table->dateTime('tanggal_verifikasi')->nullable();
        });

        DB::table('pembayaran')
            ->where('status_pembayaran', 'diverifikasi')
            ->orderBy('id_pembayaran')
            ->get(['id_pembayaran', 'tanggal_pembayaran'])
            ->each(function (object $pembayaran): void {
                $tanggal = $pembayaran->tanggal_pembayaran
                    ? \Illuminate\Support\Carbon::parse($pembayaran->tanggal_pembayaran)
                    : now();

                DB::table('pembayaran')
                    ->where('id_pembayaran', $pembayaran->id_pembayaran)
                    ->update([
                        'nomor_struk' => sprintf('SFS-%s-%06d', $tanggal->format('Ymd'), $pembayaran->id_pembayaran),
                        'tanggal_verifikasi' => $tanggal,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropUnique(['nomor_struk']);
            $table->dropColumn(['nomor_struk', 'tanggal_verifikasi']);
        });
    }
};
