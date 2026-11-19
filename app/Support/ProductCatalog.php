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
}
