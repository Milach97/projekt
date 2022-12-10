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


/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/dashboard")
 */
class UserDashboardController extends AbstractController
{
    /**
     * @Route("/", name="user_dashboard")
     */
    public function dashboard(Request $request, EntityManagerInterface $em)
    {
        // jezeli potrzebna bedzie funkcjonalnosc nadawania uprawnien
        // if(!$this->getUser()->hasPrivilegeByRoute($request->get('_route')))
        //     $this->denyAccessUnlessGranted($request);
        
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
            //TODO ilosc przypsianych podopiecznych
            // $protegeCount = $em->getRepository(Protege::class)->createQueryBuilder('pr')
            // ->select('count(pr.id)')
            // ->getQuery()
            // ->getSingleScalarResult();
        
        }




        return $this->render('user/dashboard/dashboard.html.twig', [
            'menuHighlight' => 'dashboard',
            'protectorCount' => $protectorCount,
            'protegeCount' => $protegeCount,
            'userCount' => $userCount
        ]);
    }


}
