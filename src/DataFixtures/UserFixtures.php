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


        // Utilisateur 3
        $user3 = new User();
        $user3->setEmail('paul.dupont@example.com');
        $user3->setTelephone('0612345678');
        $user3->setNom('Dupont');
        $user3->setPrenom('Paul');
        $user3->setMotDePasse($this->passwordEncoder->encodePassword(
            $user3, 'test'
        ));
        $manager->persist($user3);

        // Utilisateur 4
        $user4 = new User();
        $user4->setEmail('marie.durand@example.com');
        $user4->setTelephone('0623456789');
        $user4->setNom('Durand');
        $user4->setPrenom('Marie');
        $user4->setMotDePasse($this->passwordEncoder->encodePassword(
            $user4, 'test'
        ));
        $manager->persist($user4);

        // Utilisateur 5
        $user5 = new User();
        $user5->setEmail('alice.martin@example.com');
        $user5->setTelephone('0634567890');
        $user5->setNom('Martin');
        $user5->setPrenom('Alice');
        $user5->setMotDePasse($this->passwordEncoder->encodePassword(
            $user5, 'test'
        ));
        $manager->persist($user5);

        // Enregistrement des modifications
        $manager->flush();


        // set reference to use in other fixtures
        $this->addReference("user1", $user1);
        $this->addReference("user2", $user2);
        $this->addReference("user3", $user3);
        $this->addReference("user4", $user4);
        $this->addReference("user5", $user5);
    }
}

