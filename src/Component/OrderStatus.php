<?php

namespace App\Component;

final class OrderStatus
{
    public const STATUS_NEW = 1;
    public const STATUS_NOT_PAID = 2;
    public const STATUS_PAID = 3;
    public const STATUS_SENT_PRODUCTS = 4;
    public const STATUS_IN_PROGRESS = 5;

    public static function getStatusesArr(): array
    {
        return [
            self::STATUS_NEW => '',
            self::STATUS_NOT_PAID => '',
            self::STATUS_PAID => '',
            self::STATUS_SENT_PRODUCTS => '',
            self::STATUS_IN_PROGRESS => '',
        ];
    }

    public function getStatusStr(int $status): string
    {
        return self::getStatusesArr()[$status];
    }
}
