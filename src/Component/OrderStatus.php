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
            self::STATUS_NEW => 'New',
            self::STATUS_NOT_PAID => 'Not paid',
            self::STATUS_PAID => 'Paid',
            self::STATUS_SENT_PRODUCTS => 'Sent products',
            self::STATUS_IN_PROGRESS => 'In progress',
        ];
    }

    public static function getStatusStr(int $status): string
    {
        return self::getStatusesArr()[$status];
    }
}
