<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com'); // ou setUsername(...)
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$cGSz1WmEvdv.S7HTwU3RS.C7gmlOiVVbvxL93gWJDbsbMcxanQd5i');

        $manager->persist($admin);
        $manager->flush();
    }
}