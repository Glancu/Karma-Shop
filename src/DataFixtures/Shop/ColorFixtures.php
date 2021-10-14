<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopColor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ColorFixtures extends Fixture
{
    public static function getColorsNamesArr(): array
    {
        return [
            'Black',
            'Red',
            'Grey',
            'Pink',
            'Other'
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::getColorsNamesArr() as $name) {
            $item = new ShopColor($name);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
