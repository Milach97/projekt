<?php

namespace App\Controller\UserMobileApi;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use App\Entity\User;
use App\Entity\Protege;

/**

 * @Route("/api/v1/user")
 */
class UserController extends AbstractController
{


    /**
     * @Route("/data/{id}", name="api_usermobile_user"), methods={"GET"})
     */
    public function userDataAction(Request $request, EntityManagerInterface $em, $id): JsonResponse
    {

        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            return new JsonResponse(['message' => 'Podopieczny nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);

            
        //sprawdzenie aktualnej sesji
        $user = $protege->getUser();
        $sessionId = $request->get('sessionId');
        if ($user->getUsermobileSessionId() != null && $sessionId == $user->getUsermobileSessionId()) {




            //zapis danych do jednej tablicy
            $data = ['email' => $user->getEmail(), 
                    'name' => $user->getName(),
                    'last_name' => $user->getLastName(),
                    'phone_number' => $user->getPhoneNumber()
                ];


            //w przypadku braku danych - zwrot pustej tablicy
            return new JsonResponse($data, Response::HTTP_OK);

        }
        else
            return new JsonResponse(['message' => 'Sesja użytkownika wygasła.'], Response::HTTP_BAD_REQUEST); 
    }







    




}