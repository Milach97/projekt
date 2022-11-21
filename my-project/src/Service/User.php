<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Description of User
 *
 */
class User {

    private $em;
    private $passwordHasher;

    function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher){
        $this->em = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    function registerAction(\App\Entity\User $user){
        $em = $this->em;
        $passwordHasher = $this->passwordHasher;

        //zakoduj haslo i wrzuc usera do bazy
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $em->persist($user);
        $em->flush();

        return ['code' => 200,
                'message' => 'OK'];
    }

}
    