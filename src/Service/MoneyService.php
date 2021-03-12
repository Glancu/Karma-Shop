<?php
declare(strict_types=1);

namespace App\Service;

class MoneyService
{
    private string $currency;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    public const PRICE_DIVIDE_MULTIPLY = 100;

    public function convertIntToFloatWithCurrency(int $price): string
    {
        return $this->currency . ' ' . ($price / self::PRICE_DIVIDE_MULTIPLY);
    }

    public function convertFloatToInt($price): int
    {
        $price = str_replace($this->currency, '', $price);
        return (int)($price * self::PRICE_DIVIDE_MULTIPLY);
    }
}
