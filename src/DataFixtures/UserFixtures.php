<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    public function load(ObjectManager $manager)
    {
        // create and persist User objects
        $user1 = new User();
        $user1->setEmail('john.doe@example.com');
        $user1->setTelephone('0601020304');
        $user1->setNom('Doe');
        $user1->setPrenom('John');
        $user1->setMotDePasse('$argon2id$v=19$m=65536,t=4,p=1$Snp2S29tT3NJNm9WUXJraA$tn4f4J/UMjAr/DwRbkNWXzwS+TJZYtFgT3C93Eh31zg'); // hashed password 'password'
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('jane.doe@example.com');
        $user2->setTelephone('0602030405');
        $user2->setNom('Doe');
        $user2->setPrenom('Jane');
        $user2->setMotDePasse('$argon2id$v=19$m=65536,t=4,p=1$Snp2S29tT3NJNm9WUXJraA$tn4f4J/UMjAr/DwRbkNWXzwS+TJZYtFgT3C93Eh31zg'); // hashed password 'password'
        $manager->persist($user2);

        // flush changes to database
        $manager->flush();

        // set reference to use in other fixtures
        $this->addReference("user1", $user1);
        $this->addReference("user2", $user2);
    }
}

