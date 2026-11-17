<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class OperationalSchedule
{
    public const TIMEZONE = 'Asia/Jakarta';

    public const DAY_NAMES = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public static function settings(): array
    {
        $settings = DB::table('pengaturan_operasional')->where('id', 1)->first();

        if (! $settings) {
            throw new LogicException('Pengaturan jam operasional belum tersedia.');
        }

        $days = json_decode((string) $settings->hari_operasional, true, 512, JSON_THROW_ON_ERROR);
        $holidays = json_decode((string) $settings->tanggal_libur, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($days) || ! is_array($holidays)) {
            throw new LogicException('Pengaturan hari operasional tidak valid.');
        }

        return [
            'jam_buka' => substr((string) $settings->jam_buka, 0, 5),
            'jam_tutup' => substr((string) $settings->jam_tutup, 0, 5),
            'hari_operasional' => array_values(array_unique(array_map('intval', $days))),
            'tanggal_libur' => array_values(array_map('strval', $holidays)),
        ];
    }

    public static function current(): array
    {
        return self::calculate(self::settings(), Carbon::now(self::TIMEZONE));
    }

    public static function calculate(array $settings, \DateTimeInterface $now): array
    {
        $days = $settings['hari_operasional'] ?? [];
        $holidays = $settings['tanggal_libur'] ?? [];
        $openingTime = $settings['jam_buka'] ?? null;
        $closingTime = $settings['jam_tutup'] ?? null;

        if (
            ! is_array($days)
            || $days === []
            || array_diff($days, range(1, 7))
            || ! is_array($holidays)
            || ! is_string($openingTime)
            || ! preg_match('/^\d{2}:\d{2}$/D', $openingTime)
            || ! is_string($closingTime)
            || ! preg_match('/^\d{2}:\d{2}$/D', $closingTime)
            || $closingTime <= $openingTime
        ) {
            throw new LogicException('Pengaturan jadwal operasional tidak valid.');
        }

        $localNow = Carbon::instance($now)->setTimezone(self::TIMEZONE);
        $todayIsOperating = self::isOperatingDate($localNow, $days, $holidays);
        $shippingDate = self::nextOperatingDate(
            $localNow,
            $days,
            $holidays,
            1
        );
        $beforeOpening = $localNow->format('H:i') < $openingTime;
        $beforeClosing = $localNow->format('H:i') < $closingTime;
        $isOpenNow = $todayIsOperating
            && ! $beforeOpening
            && $beforeClosing;
        $scheduleMessage = ! $todayIsOperating
            ? 'Toko tidak beroperasi pada hari pemesanan; pesanan dijadwalkan dikirim pada hari operasional berikutnya.'
            : 'Pesanan pada hari operasional dijadwalkan dikirim pada hari operasional berikutnya.';

        $operatingDays = array_values(array_intersect(range(1, 7), $days));
        $dayNames = array_map(
            static fn (int $day): string => self::DAY_NAMES[$day],
            $operatingDays
        );
        $closedDayNames = array_map(
            static fn (int $day): string => self::DAY_NAMES[$day],
            array_values(array_diff(range(1, 7), $operatingDays))
        );

        return [
            'jam_buka' => $openingTime,
            'jam_tutup' => $closingTime,
            'hari_operasional' => $operatingDays,
            'hari_label' => implode(', ', $dayNames),
            'hari_tutup_label' => implode(', ', $closedDayNames),
            'tanggal_libur' => $holidays,
            'is_open_now' => $isOpenNow,
            'status_hari_ini' => ! $todayIsOperating
                ? 'Toko tutup hari ini'
                : ($isOpenNow
                    ? 'Toko sedang beroperasi'
                    : ($beforeOpening ? 'Toko buka pukul '.str_replace(':', '.', $openingTime).' WIB' : 'Toko tutup hari ini')),
            'tanggal_pengiriman' => $shippingDate->toDateString(),
            'label_pengiriman' => self::formatDate($shippingDate->toDateString()),
            'pesan_pengiriman' => $scheduleMessage,
        ];
    }

    public static function formatDate(string $date): string
    {
        $parsed = Carbon::createFromFormat('!Y-m-d', $date, self::TIMEZONE);

        if (! $parsed) {
            throw new LogicException('Tanggal jadwal pengiriman tidak valid.');
        }

        return self::DAY_NAMES[$parsed->isoWeekday()].', '.$parsed->format('d-m-Y');
    }

    private static function isOperatingDate(Carbon $date, array $days, array $holidays): bool
    {
        return in_array($date->isoWeekday(), $days, true)
            && ! in_array($date->toDateString(), $holidays, true);
    }

    private static function nextOperatingDate(
        Carbon $after,
        array $days,
        array $holidays,
        int $operatingDaysToSkip
    ): Carbon
    {
        $candidate = $after->copy()->startOfDay()->addDay();
        $searchLimit = (count($holidays) + $operatingDaysToSkip + 1) * 7;

        for ($offset = 0; $offset < $searchLimit; $offset++) {
            if (self::isOperatingDate($candidate, $days, $holidays)) {
                $operatingDaysToSkip--;

                if ($operatingDaysToSkip === 0) {
                    return $candidate;
                }
            }

            $candidate->addDay();
        }

        throw new LogicException('Jadwal operasional tidak memiliki hari kerja berikutnya.');
    }
}
