<?php

namespace App\Controller\UserMobileApi;

use App\Service\ProtegeService;
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
 * @Route("/api/v1/")
 */
class ProtegeController extends AbstractController
{

    /**
     * @Route("/protege/{id}/pulses/{page}", name="api_usermobile_protege_pulse"), methods={"GET"})
     */
    public function pulsePageAction(Request $request, EntityManager $em, $id, $page = 0): JsonResponse
    {

        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            return new JsonResponse(['message' => 'Podopieczny nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);

            
        //sprawdzenie aktualnej sesji
        $user = $protege->getUser();
        $sessionId = $request->get('sessionId');
        if ($user->getUsermobileSessionId() != null && $sessionId == $user->getUsermobileSessionId()) {

            // -------- PAGER START ---------
            $qb = $em->createQueryBuilder();
            $qb instanceof \Doctrine\ORM\QueryBuilder;


            //wyciagniecie danych
            $qb->select('p')
                ->from('\App\Entity\Data\Pulse', 'p')
                ->where('p.protege = :protege')
                ->setParameter('protege', $protege)
                ->orderBy('p.datetime', 'DESC');


            //PAGER
            $pager = new \App\Utils\Pager(10);
            $pager->setQueryBuilder('p', $qb);
            $pager->setPage($page);
            $pagerA = $pager->getResult();

            //czy ostatnia strona
            if($pager->getPager()['isLastPage']) {
                $isNextPage = false;
            } else {
                $isNextPage = true;
            }

            //zapis danych do jednej tablicy
            $data = ['isNextPage' => $isNextPage, 
                    'page' => $pagerA];


            //w przypadku braku danych bilingowych - zwrot pustej tablicy
            return new JsonResponse($data, Response::HTTP_OK);

        }
        else
            return new JsonResponse(['message' => 'Sesja użytkownika wygasła.'], Response::HTTP_BAD_REQUEST); 
    }



    /**
     * @Route("/protege/{id}/pulse/add", name="api_usermobile_protege_pulse_add"), methods={"GET"})
     */
    public function pulseAction(Request $request, EntityManager $em, $id): JsonResponse
    {
        $value = $request->get('value');
        $datetime = $request->get('datetime');

        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            return new JsonResponse(['message' => 'Podopieczny nie został znaleziony w naszej bazie danych.'], Response::HTTP_BAD_REQUEST);

        //sprawdzenie aktualnej sesji
        $user = $protege->getUser();
        $sessionId = $request->get('sessionId');
        if ($user->getUsermobileSessionId() != null && $sessionId == $user->getUsermobileSessionId()) {

            //serwis
            $s = new ProtegeService($em);
            $response = $s->savePulseAction($protege, $value, $datetime);

            if ($response['code' == 200])
                return new JsonResponse(Response::HTTP_OK); //200
            else
                return new JsonResponse(['message' => $response['message']], Response::HTTP_BAD_REQUEST);
                
        }
        else
            return new JsonResponse(['message' => 'Sesja użytkownika wygasła.'], Response::HTTP_BAD_REQUEST); 
    }


}