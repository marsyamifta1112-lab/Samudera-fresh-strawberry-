<?php

namespace App\Support;

class ProductCatalog
{
    public const GRADE_PRICES = [
        'A' => 80000,
        'B' => 70000,
        'C' => 50000,
    ];

    public static function isFreshStrawberry(?string $name): bool
    {
        if ($name === null) {
            return false;
        }

        $name = mb_strtolower($name);

        return (str_contains($name, 'strawberry') || str_contains($name, 'stroberi'))
            && (str_contains($name, 'segar') || str_contains($name, 'fresh'));
    }

    public static function isHematPackage(?string $name): bool
    {
        return $name !== null && str_contains(mb_strtolower($name), 'paket hemat');
    }

    public static function gradeImage(string $grade): string
    {
        return match ($grade) {
            'B' => 'strawberry-grade-b.jpg',
            'C' => 'strawberry-grade-c.jpg',
            default => 'strawberry.jpeg',
        };
    }

    public static function gradeDescription(string $grade): string
    {
        return match ($grade) {
            'A' => 'Grade A adalah pilihan premium untuk dinikmati segar atau disajikan di momen spesial.',
            'B' => 'Grade B menawarkan pilihan seimbang untuk camilan dan konsumsi sehari-hari.',
            'C' => 'Grade C adalah pilihan lebih terjangkau yang cocok juga untuk jus dan aneka olahan.',
            default => '',
        };
    }

    public static function formatKilogramStock(float|int|string|null $stock): string
    {
        return rtrim(rtrim(number_format((float) $stock, 3, ',', '.'), '0'), ',');
    }

    public static function formatKilogramInput(float|int|string|null $stock): string
    {
        return rtrim(rtrim(number_format((float) $stock, 3, '.', ''), '0'), '.');
    }
}
