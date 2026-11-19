<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const GRADES = ['A' => 80000, 'B' => 70000, 'C' => 50000];

    public function up(): void
    {
        Schema::create('produk_grade', function (Blueprint $table) {
            $table->increments('id_produk_grade');
            $table->integer('id_produk')->index();
            $table->char('grade', 1);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('stok_kg', 12, 3)->default(0);
            $table->unique(['id_produk', 'grade']);
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->char('grade', 1)->nullable();
            $table->string('satuan', 12)->nullable();
            $table->unsignedInteger('berat_gram')->nullable();
        });

        DB::table('produk')
            ->where(function ($query) {
                $query->where('nama_produk', 'like', '%Strawberry%')
                    ->orWhere('nama_produk', 'like', '%Stroberi%');
            })
            ->orderBy('id_produk')
            ->get(['id_produk', 'nama_produk', 'stok_produk', 'berat_gram'])
            ->each(function (object $product): void {
                $name = mb_strtolower($product->nama_produk);
                $isFreshStrawberry = (str_contains($name, 'strawberry') || str_contains($name, 'stroberi'))
                    && (str_contains($name, 'segar') || str_contains($name, 'fresh'));
                if (! $isFreshStrawberry) {
                    return;
                }

                $existingStock = max(0, (int) ($product->stok_produk ?? 0));
                $initialGradeAStock = $product->berat_gram
                    ? round($existingStock * (int) $product->berat_gram / 1000, 3)
                    : 0;

                foreach (self::GRADES as $grade => $price) {
                    DB::table('produk_grade')->insert([
                        'id_produk' => $product->id_produk,
                        'grade' => $grade,
                        'harga_per_kg' => $price,
                        'stok_kg' => $grade === 'A' ? $initialGradeAStock : 0,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn(['grade', 'satuan', 'berat_gram']);
        });

        Schema::dropIfExists('produk_grade');
    }
};
