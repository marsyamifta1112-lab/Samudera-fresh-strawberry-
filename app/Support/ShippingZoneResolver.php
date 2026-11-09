<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ShippingZoneResolver
{
    private const RULES = [
        [
            'name' => 'Ciwidey & Rancabali',
            'keywords' => ['ciwidey', 'rancabali'],
        ],
        [
            'name' => 'Pasirjambu & Soreang',
            'keywords' => ['pasirjambu', 'soreang'],
        ],
        [
            'name' => 'Bandung Raya (Bandung, Cimahi, Bandung Barat)',
            'keywords' => ['bandung raya', 'bandung barat', 'bandung', 'cimahi'],
        ],
        [
            'name' => 'Luar Bandung Raya',
            'keywords' => [],
        ],
    ];

    public static function rules(): array
    {
        return self::RULES;
    }

    public static function resolveName(string $address): string
    {
        $normalizedAddress = Str::lower($address);

        foreach (self::RULES as $rule) {
            foreach ($rule['keywords'] as $keyword) {
                if (str_contains($normalizedAddress, $keyword)) {
                    return $rule['name'];
                }
            }
        }

        return 'Luar Bandung Raya';
    }

    public static function resolve(Collection $zones, string $address): ?object
    {
        $name = self::resolveName($address);
        $normalizedName = Str::lower($name);

        return $zones->first(function (object $zone) use ($name, $normalizedName): bool {
            $zoneName = Str::lower($zone->nama_wilayah);

            return $zoneName === $normalizedName
                || (
                    $name === 'Bandung Raya (Bandung, Cimahi, Bandung Barat)'
                    && str_contains($zoneName, 'bandung raya')
                );
        });
    }

    public static function cost(int|float $rate, int|float $productSubtotal): int
    {
        return (int) min($rate, $productSubtotal);
    }
}
