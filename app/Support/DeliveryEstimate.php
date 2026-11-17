<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;

final class DeliveryEstimate
{
    private const TRANSIT_DAYS = [
        'Ciwidey & Rancabali' => [1, 1],
        'Pasirjambu & Soreang' => [1, 1],
        'Bandung Raya (Bandung, Cimahi, Bandung Barat)' => [2, 2],
        'Luar Bandung Raya' => [2, 3],
    ];

    public static function forOrder(
        array $settings,
        DateTimeInterface $orderedAt,
        string $zoneName,
        string $dispatchDate
    ): array {
        $schedule = OperationalSchedule::calculate($settings, $orderedAt);
        $localOrder = Carbon::instance($orderedAt)->setTimezone(OperationalSchedule::TIMEZONE);
        $startDate = $schedule['is_open_now']
            ? $localOrder->copy()->startOfDay()
            : Carbon::parse($schedule['tanggal_pengiriman'], OperationalSchedule::TIMEZONE)->startOfDay();
        $dispatchDate = Carbon::parse($dispatchDate, OperationalSchedule::TIMEZONE)->startOfDay();
        [$minimumDays, $maximumDays] = self::TRANSIT_DAYS[$zoneName] ?? self::TRANSIT_DAYS['Luar Bandung Raya'];

        $arrivalFrom = $startDate->copy()->addDays($minimumDays);
        $arrivalTo = $startDate->copy()->addDays($maximumDays);

        if ($arrivalFrom->lt($dispatchDate)) {
            $arrivalFrom = $dispatchDate->copy();
        }

        if ($arrivalTo->lt($dispatchDate)) {
            $arrivalTo = $dispatchDate->copy();
        }

        $from = $arrivalFrom->toDateString();
        $to = $arrivalTo->toDateString();

        return [
            'from' => $from,
            'to' => $to,
            'label' => self::formatRange($from, $to),
        ];
    }

    public static function forStoredOrder(object $order): ?array
    {
        if (
            empty($order->tanggal_pemesanan)
            || empty($order->wilayah_ongkir)
            || empty($order->jadwal_pengiriman)
        ) {
            return null;
        }

        return self::forOrder(
            OperationalSchedule::settings(),
            Carbon::parse($order->tanggal_pemesanan, 'UTC'),
            (string) $order->wilayah_ongkir,
            (string) $order->jadwal_pengiriman
        );
    }

    private static function formatRange(string $from, string $to): string
    {
        $fromLabel = OperationalSchedule::formatDate($from);

        return $from === $to
            ? $fromLabel
            : $fromLabel.' - '.OperationalSchedule::formatDate($to);
    }
}
