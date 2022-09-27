<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            "Foot",
            "Judo",
            "Natation",
            "Tennis",
            "Vélo",
            "Randonnée",
            "Politique",
            "Jeux vidéos",
            "Bandes dessinées",
            "Informatique",
            "Porno",
            "Boooooooobs",
            "Alcool",
            "Saucisson",
            "Cinéma",
            "Séries TV",
            "Lecture",
            "Mécanique",
            "Animaux",
            "Taxidermie"
        ];
        for($i=0; $i<count($data); $i++) {
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
