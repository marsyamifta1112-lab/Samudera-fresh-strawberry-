<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $descriptions = [
            'Strawberry Segar' => [
                'new' => 'Strawberry segar pilihan langsung dari Ciwidey. Dipetik saat matang dengan rasa manis dan sedikit asam yang menyegarkan, cocok dinikmati langsung sebagai camilan sehat.',
                'old' => 'Strawberry segar pilihan dari Ciwidey.',
            ],
            'Keripik Strawberry' => [
                'new' => 'Nikmati kerenyahan keripik strawberry dengan rasa manis dan asam yang khas. Dibuat dari buah strawberry pilihan, cocok menjadi camilan praktis untuk menemani aktivitasmu.',
                'old' => 'Keripik strawberry renyah dan lezat.',
            ],
            'Selai Strawberry' => [
                'new' => 'Selai strawberry dengan rasa manis dan segar dari buah pilihan. Teksturnya lembut dan mudah dioles, nikmat disajikan bersama roti, kue, atau camilan favoritmu.',
                'old' => 'Selai strawberry dengan rasa manis dan segar.',
            ],
        ];

        foreach ($descriptions as $nama => $description) {
            DB::table('produk')
                ->where('nama_produk', 'like', '%' . $nama . '%')
                ->update(['deskripsi' => $description['new']]);
        }
    }

    public function down(): void
    {
        $descriptions = [
            'Strawberry Segar' => 'Strawberry segar pilihan dari Ciwidey.',
            'Keripik Strawberry' => 'Keripik strawberry renyah dan lezat.',
            'Selai Strawberry' => 'Selai strawberry dengan rasa manis dan segar.',
        ];

        foreach ($descriptions as $nama => $description) {
            DB::table('produk')
                ->where('nama_produk', 'like', '%' . $nama . '%')
                ->update(['deskripsi' => $description]);
        }
    }
};
