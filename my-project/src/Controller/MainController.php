<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Entity\User;
use App\Form\User\UserType;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    // /**
    //  * @Route("/zarejestruj", name="register")
    //  */
    // public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    // {

    //     $form = $this->createForm(UserType::class);
    //     $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isValid()){

    //         //zarejestruj poprzez service
    //         // $s = new \App\Service\User($em, $passwordHasher);
    //         // $response = $s->registerAction($user);

    //         // dokonaj rejestracji uzytkownika
    //         $user = $form->getData();
    //         $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
    //         $em->persist($user);
    //         $em->flush();


    //         $this->addFlash('success', 'Rejestracja użytkownika przebiegła pomyślnie. Możesz przystąpić do logowania.');
    //         return $this->redirectToRoute('login');
    //     }
    //     return $this->render('register.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }



    /**
     * @Route("/zaloguj", name="login")
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }


    /**
     * @Route("/wyloguj", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('Dostęp do tego adresu nie powinien być możliwy.');
    }

}
