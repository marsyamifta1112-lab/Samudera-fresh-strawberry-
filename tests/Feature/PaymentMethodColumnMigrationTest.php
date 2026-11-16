<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class PaymentMethodColumnMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pembayaran', function (Blueprint $table): void {
            $table->increments('id_pembayaran');
            $table->enum('metode_pembayaran', ['BCA', 'BRI', 'BNI', 'Mandiri'])->nullable();
        });

        DB::table('pembayaran')->insert([
            'id_pembayaran' => 1,
            'metode_pembayaran' => 'BCA',
        ]);
    }

    public function test_migration_expands_payment_method_and_preserves_existing_values(): void
    {
        $migration = require base_path('database/migrations/2026_11_16_000001_expand_metode_pembayaran_column.php');
        $migration->up();

        $this->assertDatabaseHas('pembayaran', [
            'id_pembayaran' => 1,
            'metode_pembayaran' => 'BCA',
        ]);

        DB::table('pembayaran')->insert([
            'id_pembayaran' => 2,
            'metode_pembayaran' => 'QRIS',
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'id_pembayaran' => 2,
            'metode_pembayaran' => 'QRIS',
        ]);
    }

    public function test_migration_cannot_be_rolled_back_when_new_methods_are_in_use(): void
    {
        $migration = require base_path('database/migrations/2026_11_16_000001_expand_metode_pembayaran_column.php');
        $migration->up();
        DB::table('pembayaran')->insert([
            'id_pembayaran' => 2,
            'metode_pembayaran' => 'QRIS',
        ]);

        try {
            $migration->down();
            $this->fail('Rollback should not discard payment methods outside the original enum.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Kolom metode_pembayaran tidak dapat dipersempit karena ada metode pembayaran non-bank.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseHas('pembayaran', [
            'id_pembayaran' => 2,
            'metode_pembayaran' => 'QRIS',
        ]);
    }
}
