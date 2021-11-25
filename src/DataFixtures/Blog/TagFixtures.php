<?php

namespace App\DataFixtures\Blog;

use App\Entity\BlogTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
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
            $item = new BlogTag($name);
            $manager->persist($item);
        }

        $manager->flush();
    }
}
