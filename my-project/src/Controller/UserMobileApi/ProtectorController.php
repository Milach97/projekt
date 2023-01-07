<?php

namespace App\Controller\UserMobileApi;

use App\Service\ProtegeService;
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

use App\Entity\Protege;
use App\Entity\Protector;

/**
 * @Route("/api/v1")
 */
class ProtectorController extends AbstractController
{

    /**
     * @Route("/protege/{id}/protectors", name="api_usermobile_protege_protectors"), methods={"GET"})
     */
    public function protectorsAction(Request $request, EntityManagerInterface $em, $id): JsonResponse
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            return new JsonResponse(['message' => 'Podopieczny nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);


        //sprawdzenie aktualnej sesji
        $user = $protege->getUser();
        $sessionId = $request->get('sessionId');
        if ($user->getUsermobileSessionId() != null && $sessionId == $user->getUsermobileSessionId()) {

            $data = [];
            $protectors = $protege->getProtector()->getValues();
            foreach($protectors as $protector){
                $d = [
                    'name' => $protector->getUser()->getName(),
                    'last_name' => $protector->getUser()->getLastName(),
                    'email' => $protector->getUser()->getEmail(),
                    'phone_number' => $protector->getUser()->getPhoneNumber(),
                ];
                array_push($data, $d);
            }


            //w przypadku braku danych - zwrot pustej tablicy
            return new JsonResponse($data, Response::HTTP_OK);

        }
        else
            return new JsonResponse(['message' => 'Sesja użytkownika wygasła.'], Response::HTTP_BAD_REQUEST); 

    }


}