<?php

namespace App\DataFixtures;

use App\Entity\Trajet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserFixtures;

class TrajetFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // create and persist Trajet objects
        $trajet1 = new Trajet();
        $trajet1->setVilleDepart('Paris');
        $trajet1->setVilleDestination('Marseille');
        $trajet1->setDateDepart(new \DateTime('2023-03-20 08:00:00'));
        $trajet1->setDateArrivee(new \DateTime('2023-03-20 12:00:00'));
        $trajet1->setNombrePlaces(3);
        $trajet1->setConducteur($manager->merge($this->getReference("user1")));
        $manager->persist($trajet1);

        $trajet2 = new Trajet();
        $trajet2->setVilleDepart('Lyon');
        $trajet2->setVilleDestination('Toulouse');
        $trajet2->setDateDepart(new \DateTime('2023-03-22 14:00:00'));
        $trajet2->setDateArrivee(new \DateTime('2023-03-22 18:00:00'));
        $trajet2->setNombrePlaces(2);
        $trajet2->setConducteur($manager->merge($this->getReference("user2")));
        $manager->persist($trajet2);


        $manager->flush();
    }
}
