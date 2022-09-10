<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopBrand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public static function getBrandsNamesArr(): array
    {
        return [
            'Gionee',
            'Nike',
            'Adidas',
            'Puma',
            'Calvin Klein',
            'New Balance',
            'Tommy Hilfiger'
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::getBrandsNamesArr() as $name) {
            $item = new ShopBrand($name);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
