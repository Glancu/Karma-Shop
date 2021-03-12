<?php
declare(strict_types=1);

namespace App\Service;

class MoneyService
{
    public const PRICE_DIVIDE_MULTIPLY = 100;

    public static function convertIntToFloatDivideBy(int $price, int $divideBy = self::PRICE_DIVIDE_MULTIPLY): float
    {
        return (float)($price / $divideBy);
    }

    public static function convertFloatToIntMultiplyBy($price, int $multipleBy = self::PRICE_DIVIDE_MULTIPLY): int
    {
        return (int)($price * $multipleBy);
    }
}
