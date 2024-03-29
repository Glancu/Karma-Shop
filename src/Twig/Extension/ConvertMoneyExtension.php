<?php

namespace App\Twig\Extension;

use App\Service\MoneyService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConvertMoneyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('convert_money_to_float', [$this, 'convertMoneyToFloat'])
        ];
    }

    public function convertMoneyToFloat($money): float
    {
        return MoneyService::convertIntToFloat($money);
    }
}
