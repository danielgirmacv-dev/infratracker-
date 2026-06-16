<?php

namespace App\Support;

class QuantityUnit
{
    /** @var list<string> */
    public const OPTIONS = [
        'BAG',
        'Kg',
        'Iitter',
        'EA',
        'PCS',
        'ml',
        'mm',
        'Barl',
        'M2',
        'M3',
        'Km/hr',
        'Km',
        'Cm',
    ];

    /**
     * @return list<string>
     */
    public static function options(): array
    {
        return self::OPTIONS;
    }

    public static function isValid(?string $unit): bool
    {
        return $unit === null || $unit === '' || in_array($unit, self::OPTIONS, true);
    }
}
