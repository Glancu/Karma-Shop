<?php
declare(strict_types=1);

namespace App\Service;

class MoneyService
{
    public const PRICE_DIVIDE_MULTIPLY = 100;

    public function convertIntToFloat(int $price): string
    {
        return number_format($price / self::PRICE_DIVIDE_MULTIPLY, 2, '.', ',');
    }

    public function convertFloatToInt($price): int
    {
        return (int)($price * self::PRICE_DIVIDE_MULTIPLY);
    }
}
