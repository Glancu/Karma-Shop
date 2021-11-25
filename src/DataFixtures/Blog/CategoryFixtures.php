<?php

namespace App\DataFixtures\Blog;

use App\Entity\BlogCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public static function getNamesArr(): array
    {
        return [
            'Technology',
            'Lifestyle',
            'Fashion',
            'Art',
            'Food',
            'Architecture',
            'Adventure'
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::getNamesArr() as $name) {
            $item = new BlogCategory($name);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
