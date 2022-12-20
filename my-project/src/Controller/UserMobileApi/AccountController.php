<?php

namespace App\Controller\UserMobileApi;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


/**
 * @Security("is_granted('ROLE_USERMOBILEAPI')")
 * @Route("/api/v1/account")
 */
class AccountController extends AbstractController
{


    //Logowanie do panelu poprzez kod dostepu
    /**
     * @Route("/login/{email}", name="api_usermobile_account_login"), methods={"GET"})
     */
    public function loginAction(Request $request, EntityManager $em, $email): JsonResponse 
    {

        //stala uzywana do szyfrowania 
        $constant = '4gmHNIl7RHx0e6TGDQcXsLDZ4mb7tPTj2Tj23t8UBDTicRUCGvX58E4TM56lEucx';

        //zaszyfrowane haslo w requescie -> md5($timestamp.$constant.$pin);
        $timestamp = $request->get('timestamp');
        $password = $request->get('password');

        //spr czy timestamp jest aktualny ( nie starszy niz 2 minuty)
        if((time() - $timestamp) < 120) {     //120 seconds


            //znajdz uzytkownika po podanym emailu
            $user = $em->getRepository(User::class)->findOneBy(array('email' => $email));
            if(!$user)
                return new JsonResponse(['message' => 'Użytkownik nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);

            if(!in_array('ROLE_PROTEGE', $user->getRoles())){
                return new JsonResponse(['message' => 'Użytkownik nie jest przypisany do odpowiedniej roli.'], Response::HTTP_BAD_REQUEST);
            }

            //zlozenie i porownanie hasla
            if($password == md5($timestamp.$constant.$user->getUsermobilePassword())){
                //jezeli sie zgadza -> nadaj zakodowane id sesji
                $user->setUsermobileSessionId(md5($password));
                $em->persist($user);
                $em->flush();

                return new JsonResponse(Response::HTTP_OK);  //OK 200
            }
            else{
                return new JsonResponse(['message' => 'Wprowadzono błędny kod pin.'], Response::HTTP_BAD_REQUEST);
            }
        }


        //blad - niezgodny czas
        return new JsonResponse(['message' => 'Przekroczono limit czasowy.'], Response::HTTP_BAD_REQUEST);
    }



    //Zmiana pinu aplikacji
    /**
     * @Route("/passwordChange/{email}", name="api_usermobile_account_passwordChange"), methods={"PATCH"}
     */
    public function passwordChangeAction(Request $request, EntityManager $em, $email)
    {
        //podaj posolone stare haslo - md5(timestamp.haslo.stala)
        $oldPassword = $request->get('oldPassword');
        $newPassword = $request->get('newPassword');
        $sessionId = $request->get('sessionId');
        $timestamp = $request->get('timestamp');

        $constant = '4gmHNIl7RHx0e6TGDQcXsLDZ4mb7tPTj2Tj23t8UBDTicRUCGvX58E4TM56lEucx';


        //spr czy timestamp jest aktualny ( nie starszy niz minuta)
        if((time() - $timestamp) < 120) {     //120 seconds

            //znajdz uzytkownika po podanym emailu
            $user = $em->getRepository(User::class)->findOneBy(array('email' => $email()));
            if(!$user)
                return new JsonResponse(['message' => 'Użytkownik nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);
            if(!in_array('ROLE_PROTEGE', $user->getRoles())){
                return new JsonResponse(['message' => 'Użytkownik nie jest przypisany do odpowiedniej roli.'], Response::HTTP_BAD_REQUEST);
            }

    
            //sprawdzenie aktualnej sesji
            if ($user->getUsermobileSessionId() != null && $sessionId == $user->getUsermobileSessionId()) {
                //sprawdzenie starego hasla
                if ($oldPassword == md5($timestamp.$user->getUsermobilePasswordCode().$constant)) {

                    //nadaj nowe haslo i sesje uzytkownika do bazy
                    $user->setUsermobilePasswordCode($newPassword);
                    $user->setUsermobileSessionId(md5(md5($timestamp.$newPassword.$constant)));
                    $em->persist($user);
                    $em->flush();
        
                    return new JsonResponse(Response::HTTP_OK);  //200
                } else 
                    return new JsonResponse(['message' => 'Podane hasło użytkownika nie jest zgodne z obecnie używanym.'], Response::HTTP_BAD_REQUEST); 
            } else 
                return new JsonResponse(['message' => 'Sesja użytkownika wygasła.'], Response::HTTP_BAD_REQUEST);   
        } else
            return new JsonResponse(['message' => 'Przekroczono limit czasowy.'], Response::HTTP_BAD_REQUEST);        

    }    
    


    //wyloguj z aplikacji
    /**
     * @Route("/logout/{email}", name="api_usermobile_account_logout"), methods={"PATCH"})
     */
    public function logoutAction(Request $request, EntityManager $em, $email){

        //znajdz uzytkownika po podanym emailu
        $user = $em->getRepository(User::class)->findOneBy(array('email' => $email()));
        if(!$user)
            return new JsonResponse(['message' => 'Użytkownik nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);
        if(!in_array('ROLE_PROTEGE', $user->getRoles())){
            return new JsonResponse(['message' => 'Użytkownik nie jest przypisany do odpowiedniej roli.'], Response::HTTP_BAD_REQUEST);
        }

        //wyzeruj sessionId
        $user->setUsermobileSessionId(null);
        $em->persist($user);
        $em->flush();
    
        //przekieruj do widoku logowania/ zamknij aplikacje - po stronie provemy
        return new JsonResponse(Response::HTTP_OK);  //200

    }


}