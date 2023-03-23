<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Trajet;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{


    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
    }

    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
    }
}