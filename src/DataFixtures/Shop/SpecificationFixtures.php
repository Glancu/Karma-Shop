<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopProductSpecification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class SpecificationFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $specificationTypes = $manager->getRepository('App:ShopProductSpecificationType')->findAll();

        foreach ($specificationTypes as $item) {
            $item = new ShopProductSpecification($item, random_int(10, 30));
            $manager->persist($item);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SpecificationTypeFixtures::class,
        ];
    }
}
