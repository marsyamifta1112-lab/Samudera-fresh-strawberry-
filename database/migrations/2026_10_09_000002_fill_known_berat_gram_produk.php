<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('produk')
            ->whereNull('berat_gram')
            ->orderBy('id_produk')
            ->chunk(100, function ($produk) {
                foreach ($produk as $item) {
                    $teksProduk = $item->nama_produk . ' ' . ($item->deskripsi ?? '');

                    if (!preg_match(
                        '/(?<!\d)(\d+(?:[.,]\d+)?)\s*(kilograms?|kg|grams?|gr|g)\b/i',
                        $teksProduk,
                        $hasil
                    )) {
                        continue;
                    }

                    $berat = (float) str_replace(',', '.', $hasil[1]);
                    $satuan = strtolower($hasil[2]);

                    if (in_array($satuan, ['kg', 'kilogram', 'kilograms'], true)) {
                        $berat *= 1000;
                    }

                    DB::table('produk')
                        ->where('id_produk', $item->id_produk)
                        ->whereNull('berat_gram')
                        ->update(['berat_gram' => (int) round($berat)]);
                }
            });
    }

    public function down(): void
    {
    }
};
