<?php

namespace Tests\Feature;

use App\Support\DeliveryEstimate;
use App\Support\OperationalSchedule;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DeliveryEstimateTest extends TestCase
{
    public static function zoneEstimates(): array
    {
        return [
            'Ciwidey and Rancabali arrive in one calendar day' => [
                'Ciwidey & Rancabali',
                '2026-11-17',
                '2026-11-17',
                'Selasa, 17-11-2026',
            ],
            'nearby zones arrive in one calendar day' => [
                'Pasirjambu & Soreang',
                '2026-11-17',
                '2026-11-17',
                'Selasa, 17-11-2026',
            ],
            'Bandung Raya arrives in two calendar days' => [
                'Bandung Raya (Bandung, Cimahi, Bandung Barat)',
                '2026-11-18',
                '2026-11-18',
                'Rabu, 18-11-2026',
            ],
            'outside Bandung Raya has a two to three day range' => [
                'Luar Bandung Raya',
                '2026-11-18',
                '2026-11-19',
                'Rabu, 18-11-2026 - Kamis, 19-11-2026',
            ],
        ];
    }

    #[DataProvider('zoneEstimates')]
    public function test_zone_estimates_use_the_configured_calendar_day_ranges(
        string $zone,
        string $expectedFrom,
        string $expectedTo,
        string $expectedLabel
    ): void {
        $orderedAt = Carbon::parse('2026-11-16 10:00:00', OperationalSchedule::TIMEZONE);

        $estimate = DeliveryEstimate::forOrder(
            $this->settings(),
            $orderedAt,
            $zone,
            '2026-11-17'
        );

        $this->assertSame($expectedFrom, $estimate['from']);
        $this->assertSame($expectedTo, $estimate['to']);
        $this->assertSame($expectedLabel, $estimate['label']);
    }

    public function test_estimate_starts_from_the_next_operational_day_when_ordered_on_a_holiday(): void
    {
        $estimate = DeliveryEstimate::forOrder(
            $this->settings(['2026-11-16']),
            Carbon::parse('2026-11-15 12:00:00', OperationalSchedule::TIMEZONE),
            'Pasirjambu & Soreang',
            '2026-11-17'
        );

        $this->assertSame('2026-11-18', $estimate['from']);
        $this->assertSame('2026-11-18', $estimate['to']);
    }

    public function test_estimate_does_not_precede_the_dispatch_date(): void
    {
        $estimate = DeliveryEstimate::forOrder(
            $this->settings(),
            Carbon::parse('2026-11-16 22:00:00', OperationalSchedule::TIMEZONE),
            'Pasirjambu & Soreang',
            '2026-11-17'
        );

        $this->assertSame('2026-11-18', $estimate['from']);
        $this->assertSame('2026-11-18', $estimate['to']);
    }

    private function settings(array $holidays = []): array
    {
        return [
            'jam_buka' => '08:00',
            'jam_tutup' => '17:00',
            'hari_operasional' => [1, 2, 3, 4, 5, 6],
            'tanggal_libur' => $holidays,
        ];
    }
}
