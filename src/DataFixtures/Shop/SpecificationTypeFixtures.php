<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopProductSpecificationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SpecificationTypeFixtures extends Fixture
{
    public static function getSpecificationTypesNamesArr(): array
    {
        return [
            'Weight',
            'Height',
            'Width',
            'Durability'
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::getSpecificationTypesNamesArr() as $key => $name) {
            $item = new ShopProductSpecificationType($name, $key);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
