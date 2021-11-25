<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 8; $i++) {
            $item = new Comment(
                $faker->text(10),
                $faker->email,
                $faker->text(500),
                $faker->text(10),
                true
            );
            $manager->persist($item);
        }

        $manager->flush();
    }
}
