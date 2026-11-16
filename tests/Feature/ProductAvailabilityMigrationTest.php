<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductAvailabilityMigrationTest extends TestCase
{
    public function test_migration_marks_existing_products_available_and_preserves_rows(): void
    {
        Schema::create('produk', function (Blueprint $table): void {
            $table->increments('id_produk');
            $table->string('nama_produk');
            $table->integer('stok_produk')->default(0);
        });
        DB::table('produk')->insert([
            'id_produk' => 1,
            'nama_produk' => 'Stroberi Lama',
            'stok_produk' => 4,
        ]);

        $migration = require base_path('database/migrations/2026_11_16_000002_add_is_available_to_produk_table.php');
        $migration->up();

        $this->assertDatabaseHas('produk', [
            'id_produk' => 1,
            'nama_produk' => 'Stroberi Lama',
            'stok_produk' => 4,
            'is_available' => true,
        ]);

        DB::table('produk')->insert([
            'id_produk' => 2,
            'nama_produk' => 'Stroberi Baru',
            'stok_produk' => 3,
        ]);
        $this->assertDatabaseHas('produk', [
            'id_produk' => 2,
            'is_available' => true,
        ]);
    }
}
