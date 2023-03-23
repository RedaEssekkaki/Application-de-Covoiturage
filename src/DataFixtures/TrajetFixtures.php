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
        $trajet1 = new Trajet();
        $trajet1->setVilleDepart('Paris');
        $trajet1->setVilleDestination('Marseille');
        $trajet1->setDateDepart((new \DateTime())->modify('+1 day')->setTime(8, 0, 0));
        $trajet1->setDateArrivee((new \DateTime())->modify('+1 day')->setTime(12, 0, 0));
        $trajet1->setNombrePlaces(3);
        $trajet1->setConducteur($manager->merge($this->getReference("user1")));
        $manager->persist($trajet1);

        $trajet2 = new Trajet();
        $trajet2->setVilleDepart('Lyon');
        $trajet2->setVilleDestination('Toulouse');
        $trajet2->setDateDepart((new \DateTime())->modify('+1 day')->setTime(14, 0, 0));
        $trajet2->setDateArrivee((new \DateTime())->modify('+1 day')->setTime(18, 0, 0));
        $trajet2->setNombrePlaces(2);
        $trajet2->setConducteur($manager->merge($this->getReference("user2")));
        $manager->persist($trajet2);


        $trajet3 = new Trajet();
        $trajet3->setVilleDepart('Bordeaux');
        $trajet3->setVilleDestination('Nantes');
        $trajet3->setDateDepart((new \DateTime())->modify('+2 days')->setTime(10, 0, 0));
        $trajet3->setDateArrivee((new \DateTime())->modify('+2 days')->setTime(14, 0, 0));
        $trajet3->setNombrePlaces(4);
        $trajet3->setConducteur($manager->merge($this->getReference("user5")));
        $manager->persist($trajet3);

        $trajet4 = new Trajet();
        $trajet4->setVilleDepart('Lille');
        $trajet4->setVilleDestination('Strasbourg');
        $trajet4->setDateDepart((new \DateTime())->modify('+3 days')->setTime(9, 0, 0));
        $trajet4->setDateArrivee((new \DateTime())->modify('+3 days')->setTime(13, 0, 0));
        $trajet4->setNombrePlaces(1);
        $trajet4->setConducteur($manager->merge($this->getReference("user4")));
        $manager->persist($trajet4);

        $manager->flush();
    }
}
