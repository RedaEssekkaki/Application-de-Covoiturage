<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        // create and persist User objects
        $user1 = new User();
        $user1->setTelephone('0601020304');
        $user1->setNom('Doe');
        $user1->setPrenom('John');
        $user1->setEmail("test@gmail.com")
            ->setRoles(["ROLE_USER,ROLE_ADMIN"])
            ->setMotDePasse($this->passwordEncoder->encodePassword(
                $user1, 'test'
            ));
        $manager->persist($user1);



        $user2 = new User();
        $user2->setEmail('jane.doe@example.com');
        $user2->setTelephone('0602030405');
        $user2->setNom('Doe');
        $user2->setPrenom('Jane');
        $user2->setMotDePasse($this->passwordEncoder->encodePassword(
            $user2, 'test'
        ));
        $manager->persist($user2);

        // flush changes to database
        $manager->flush();

        // set reference to use in other fixtures
        $this->addReference("user1", $user1);
        $this->addReference("user2", $user2);
    }
}

