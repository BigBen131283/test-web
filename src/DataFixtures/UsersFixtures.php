<?php

namespace App\DataFixtures;

use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        // $timestamp = new DateTimeImmutable();
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 100; $i++) {
            $user = new Users();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setRole('10');
            $user->setAge($faker->biasedNumberBetween(18, 99));
            $user->setCreatedAt($faker->dateTimeThisYear('now', 'Europe/Paris'));
            
            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        //TODO: implement getGroups() method.
        return ['users'];
    }
}
