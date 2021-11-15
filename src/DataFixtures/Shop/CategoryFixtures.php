<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public static function getCategoriesNamesArr(): array
    {
        return [
            'Sneakers',
            'Shoes'
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::getCategoriesNamesArr() as $name) {
            $item = new ShopCategory($name);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
