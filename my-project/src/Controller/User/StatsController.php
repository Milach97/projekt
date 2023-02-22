<?php

namespace App\Controller\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Protector;
use App\Entity\Protege;
use App\Entity\User;

use App\Entity\Data\Pulse;
use App\Entity\Data\Weight;
use App\Entity\Data\Saturation;
use App\Entity\Data\Pressure;


/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/statystyki")
 */
class StatsController extends AbstractController
{
    /**
     * @Route("/", name="user_stats")
     */
    public function dashboard(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $protectorCount = $protegeCount = $userCount = null;

        //jezeli admin
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            //ilosc opiekunow ogole,
            $protectorCount = $em->getRepository(Protector::class)->createQueryBuilder('p')
                ->select('count(p.id)')
                ->getQuery()
                ->getSingleScalarResult();
            
            //ilosc podopiecznych ogolem
            $protegeCount = $em->getRepository(Protege::class)->createQueryBuilder('pr')
                ->select('count(pr.id)')
                ->getQuery()
                ->getSingleScalarResult();
            
    
            //ilosc uzytkownikow ogolem
            $userCount = $em->getRepository(User::class)->createQueryBuilder('u')
                ->select('count(u.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }
        //jezeli opiekun
        elseif (in_array('ROLE_PROTECTOR', $user->getRoles())){
            // ilosc przypsianych podopiecznych
            $protegeCount = $em->getRepository(Protege::class)->createQueryBuilder('pr')
                ->select('count(pr.id)')
                ->join('pr.protector', 'pro')
                ->where("pro.id = :id")
                ->setParameter('id', $this->getUser()->getProtector()->getId())
                ->getQuery()
                ->getSingleScalarResult();
        
        }




        return $this->render('user/stats.html.twig', [
            'menuHighlight' => 'statistics',
            'protectorCount' => $protectorCount,
            'protegeCount' => $protegeCount,
            'userCount' => $userCount
        ]);
    }


}
