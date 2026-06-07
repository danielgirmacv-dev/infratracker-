<?php

namespace App\Support;

class MoneyFormat
{
    public static function format(null|string|int|float $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        $numeric = (float) str_replace(',', '', (string) $amount);
        $decimals = fmod($numeric, 1.0) === 0.0 ? 0 : 2;

        return number_format($numeric, $decimals);
    }

    public static function parse(null|string|int|float $amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        $clean = str_replace(',', '', trim((string) $amount));

        if ($clean === '') {
            return null;
        }

        if (!is_numeric($clean)) {
            return null;
        }

        return $clean;
    }
}
