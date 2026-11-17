<?php

namespace Tests\Feature;

use App\Support\OperationalSchedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OperationalScheduleTest extends TestCase
{
    public function test_weekday_orders_before_noon_are_scheduled_for_the_next_operating_day(): void
    {
        foreach (['00:00', '07:59', '08:00', '11:59'] as $time) {
            $schedule = OperationalSchedule::calculate(
                $this->settings(),
                Carbon::parse('2026-11-16 '.$time, OperationalSchedule::TIMEZONE)
            );

            $this->assertSame('2026-11-17', $schedule['tanggal_pengiriman'], $time);
        }
    }

    public function test_weekday_orders_at_or_after_noon_are_scheduled_for_the_next_operating_day(): void
    {
        foreach (['12:00', '17:00', '22:00', '23:59'] as $time) {
            $schedule = OperationalSchedule::calculate(
                $this->settings(),
                Carbon::parse('2026-11-16 '.$time, OperationalSchedule::TIMEZONE)
            );

            $this->assertSame('2026-11-17', $schedule['tanggal_pengiriman'], $time);
        }
    }

    public function test_utc_order_timestamp_is_converted_to_jakarta_for_shipping_schedule(): void
    {
        $schedule = OperationalSchedule::calculate(
            $this->settings(),
            Carbon::parse('2026-11-15 17:00:00', 'UTC')
        );

        $this->assertSame('2026-11-17', $schedule['tanggal_pengiriman']);
        $this->assertSame(
            'Pesanan pada hari operasional dijadwalkan dikirim pada hari operasional berikutnya.',
            $schedule['pesan_pengiriman']
        );
    }

    public function test_orders_skip_holidays_when_finding_next_operating_day(): void
    {
        $settings = $this->settings();
        $settings['tanggal_libur'] = ['2026-11-17'];

        $schedule = OperationalSchedule::calculate(
            $settings,
            Carbon::parse('2026-11-16 12:00', OperationalSchedule::TIMEZONE)
        );

        $this->assertSame('2026-11-18', $schedule['tanggal_pengiriman']);
    }

    public function test_saturday_orders_are_scheduled_for_the_next_operating_day_regardless_of_time(): void
    {
        foreach (['00:01', '11:59', '12:00', '23:59'] as $time) {
            $schedule = OperationalSchedule::calculate(
                $this->settings(),
                Carbon::parse('2026-11-21 '.$time, OperationalSchedule::TIMEZONE)
            );

            $this->assertSame('2026-11-23', $schedule['tanggal_pengiriman'], $time);
        }
    }

    public function test_closed_days_and_consecutive_holidays_are_skipped(): void
    {
        $settings = $this->settings();
        $settings['tanggal_libur'] = ['2026-11-16', '2026-11-17'];

        $schedule = OperationalSchedule::calculate(
            $settings,
            Carbon::parse('2026-11-15 12:00', OperationalSchedule::TIMEZONE)
        );

        $this->assertSame('2026-11-18', $schedule['tanggal_pengiriman']);
        $this->assertSame('Rabu, 18-11-2026', $schedule['label_pengiriman']);
    }

    public function test_schedule_settings_migration_preserves_existing_orders(): void
    {
        Schema::create('pesanan', function (Blueprint $table): void {
            $table->increments('id_pesanan');
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->date('tanggal_pemrosesan')->nullable();
            $table->decimal('total_harga', 12, 2)->nullable();
        });
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'tanggal_pemesanan' => '2026-11-16 10:00:00',
            'tanggal_pemrosesan' => '2026-11-16',
            'total_harga' => 25000,
        ]);

        $migration = require base_path('database/migrations/2026_11_16_000003_create_operational_schedule_settings.php');
        $migration->up();

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'tanggal_pemesanan' => '2026-11-16 10:00:00',
            'tanggal_pemrosesan' => '2026-11-16',
            'total_harga' => 25000,
        ]);
        $this->assertDatabaseHas('pengaturan_operasional', [
            'id' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '17:00:00',
        ]);
        $this->assertTrue(Schema::hasColumn('pesanan', 'tanggal_pemrosesan'));

        $shippingScheduleMigration = require base_path('database/migrations/2026_11_16_000004_add_scheduled_shipping_date_to_pesanan.php');
        $shippingScheduleMigration->up();

        $this->assertTrue(Schema::hasColumn('pesanan', 'jadwal_pengiriman'));
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'tanggal_pemesanan' => '2026-11-16 10:00:00',
            'tanggal_pemrosesan' => '2026-11-16',
            'total_harga' => 25000,
        ]);
    }

    private function settings(): array
    {
        return [
            'jam_buka' => '08:00',
            'jam_tutup' => '17:00',
            'hari_operasional' => [1, 2, 3, 4, 5, 6],
            'tanggal_libur' => [],
        ];
    }
}
