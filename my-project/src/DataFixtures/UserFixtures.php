<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
#use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Protege;
use App\Entity\Protector;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $roles])
        {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setRoles($roles);

            //jezeli dodawany jest podopieczny - stworz w bazie
            if(in_array("ROLE_PROTEGE", $roles)){
                $protege = new Protege();
                $protege->setUser($user);
                $protege->setHeight(177);
                $protege->setGender('MALE');
                $manager->persist($protege);
            }
            elseif (in_array('ROLE_PROTECTOR', $roles)){
                $protector = new Protector();
                $protector->setUser($user);
                $manager->persist($protector);
            }
        
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [

            ['Jan', 'Nowak', 'admin@sample.com', 'wojtek', ['ROLE_ADMIN']],
            ['Kacper', 'Malinowski', 'protector@sample.com', 'wojtek', ['ROLE_PROTECTOR']],
            ['Tadeusz', 'Kamiński', 'user@sample.com', 'wojtek', ['ROLE_PROTEGE']]

        ];
    }

}

