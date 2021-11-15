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
            'Asus',
            'Apple',
            'Acer',
            'HP',
            'Dell',
            'MSI'
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
