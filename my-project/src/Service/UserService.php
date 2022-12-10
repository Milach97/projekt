<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use \App\Entity\User;

/**
 * Description of User
 *
 */
class UserService {

    private $em;
    private $passwordHasher;

    function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher){
        $this->em = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    function registerAction(User $user){
        $em = $this->em;
        $passwordHasher = $this->passwordHasher;

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $em->persist($user);
        $em->flush();

        return ['code' => 200,
                'message' => 'OK'];
    }


    function changePasswordAction(User $user, $newPassord){
        $em = $this->em;
        $passwordHasher = $this->passwordHasher;

        $user->setPassword($passwordHasher->hashPassword($user, $newPassord));
        $em->persist($user);
        $em->flush();

        return [
            'code' => 200,
            'message' => 'Hasło zostało pomyślnie zmienione.'
        ];
    }

}
    