<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShippingZoneMigrationTest extends TestCase
{
    public function test_migration_creates_default_shipping_zones_and_preserves_existing_orders(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->decimal('ongkir', 12, 2)->nullable();
            $table->decimal('total_harga', 12, 2);
        });
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'ongkir' => 0,
            'total_harga' => 42500,
        ]);

        $migration = require base_path('database/migrations/2026_10_29_000002_create_ongkir_wilayah_table.php');
        $migration->up();

        $this->assertSame(4, DB::table('ongkir_wilayah')->count());
        $this->assertDatabaseHas('ongkir_wilayah', [
            'nama_wilayah' => 'Ciwidey & Rancabali',
            'tarif' => 10000,
        ]);
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'ongkir' => 0,
            'total_harga' => 42500,
            'wilayah_ongkir' => null,
        ]);

        $migration->down();

        $this->assertFalse(Schema::hasTable('ongkir_wilayah'));
        $this->assertFalse(Schema::hasColumn('pesanan', 'wilayah_ongkir'));
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => 1, 'total_harga' => 42500]);
    }

    public function test_admin_password_request_table_rename_preserves_existing_requests(): void
    {
        Schema::create('admin_password_recovery_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('reviewer_id')->nullable();
            $table->string('token_hash', 64)->nullable();
            $table->string('status', 30);
            $table->dateTime('requested_at');
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();
        });
        DB::table('admin_password_recovery_requests')->insert([
            'id' => 1,
            'admin_id' => 10,
            'reviewer_id' => 11,
            'token_hash' => str_repeat('a', 64),
            'status' => 'Disetujui',
            'requested_at' => '2026-10-29 10:00:00',
            'reviewed_at' => '2026-10-29 10:05:00',
            'expires_at' => '2026-10-29 11:00:00',
            'created_at' => '2026-10-29 10:00:00',
            'updated_at' => '2026-10-29 10:05:00',
        ]);

        $migration = require base_path('database/migrations/2026_10_29_000003_rename_admin_password_recovery_requests_table.php');
        $migration->up();

        $this->assertFalse(Schema::hasTable('admin_password_recovery_requests'));
        $this->assertDatabaseHas('permintaan_reset_password_admin', [
            'id' => 1,
            'admin_id' => 10,
            'reviewer_id' => 11,
            'status' => 'Disetujui',
        ]);

        $migration->down();

        $this->assertFalse(Schema::hasTable('permintaan_reset_password_admin'));
        $this->assertDatabaseHas('admin_password_recovery_requests', [
            'id' => 1,
            'admin_id' => 10,
            'reviewer_id' => 11,
            'status' => 'Disetujui',
        ]);
    }
}
