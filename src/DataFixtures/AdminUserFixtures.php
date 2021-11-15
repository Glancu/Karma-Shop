<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder) {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new AdminUser('email@admin.com', 'admin', '');
        $adminUser->setRoles(['ROLE_ADMIN']);

        $encodedPassword = $this->userPasswordEncoder->encodePassword($adminUser, 'admin1');
        $adminUser->setPassword($encodedPassword);

        $manager->persist($adminUser);
        $manager->flush();
    }
}
