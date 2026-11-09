<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderShippingMigrationTest extends TestCase
{
    public function test_migration_adds_zero_shipping_to_existing_orders_without_changing_their_total(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->decimal('total_harga', 12, 2);
        });
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'total_harga' => 42500,
        ]);

        $migration = require base_path('database/migrations/2026_10_29_000001_add_ongkir_to_pesanan_table.php');
        $migration->up();

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'total_harga' => 42500,
            'ongkir' => 0,
        ]);

        $migration->down();

        $this->assertFalse(Schema::hasColumn('pesanan', 'ongkir'));
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'total_harga' => 42500,
        ]);
    }
}
