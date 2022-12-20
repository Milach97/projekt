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
            // ilosc przypsianych podopiecznych
            $protegeCount = $em->getRepository(Protege::class)->createQueryBuilder('pr')
                ->select('count(pr.id)')
                ->join('pr.protector', 'pro')
                ->where("pro.id = :id")
                ->setParameter('id', $this->getUser()->getProtector()->getId())
                ->getQuery()
                ->getSingleScalarResult();
        
        }




        return $this->render('user/dashboard/dashboard.html.twig', [
            'menuHighlight' => 'dashboard',
            'protectorCount' => $protectorCount,
            'protegeCount' => $protegeCount,
            'userCount' => $userCount
        ]);
    }


    /**
     * @Route("/wykres", name="user_dashboard_chart")
     */
    public function dashboardChart(Request $request, EntityManagerInterface $em)
    {
        $begin = new \DateTime('now - 2 weeks');
        $end = new \DateTime('now');
        $end = $end->modify('+1 day'); 
        $interval = new \DateInterval('P1D');

        $daterange = new \DatePeriod($begin, $interval ,$end);
        $data = [];


        //pobierz dane w zaleznosci od roli uzytkownika
        $roles = $this->getUser()->getRoles();


        if(in_array('ROLE_ADMIN' ,$roles)){
            //TODO
        }
        elseif(in_array('ROLE_PROTECTOR', $roles)){
            //TODO
        }
        elseif(in_array('ROLE_PROTEGE', $roles)){
            foreach($daterange as $date){
                $date = $date->format("Y-m-d");
                //pobierz ilosc danych na ten dzien
                $pulseCount = $em->getRepository(Pulse::class)->createQueryBuilder('p')
                    ->select('count(p.id)')
                    ->where("p.protege = :protege")
                    ->setParameter('protege', $this->getUser()->getProtege())
                    ->andWhere("p.datetime = :date")
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getSingleScalarResult();

                $weightCount = $em->getRepository(Weight::class)->createQueryBuilder('w')
                    ->select('count(w.id)')
                    ->where("w.protege = :protege")
                    ->setParameter('protege', $this->getUser()->getProtege())
                    ->andWhere("w.datetime = :date")
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getSingleScalarResult();

                $saturationCount = $em->getRepository(Weight::class)->createQueryBuilder('s')
                    ->select('count(s.id)')
                    ->where("s.protege = :protege")
                    ->setParameter('protege', $this->getUser()->getProtege())
                    ->andWhere("s.datetime = :date")
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getSingleScalarResult();

                $pressureCount = $em->getRepository(Pressure::class)->createQueryBuilder('pr')
                    ->select('count(pr.id)')
                    ->where("pr.protege = :protege")
                    ->setParameter('protege', $this->getUser()->getProtege())
                    ->andWhere("pr.datetime = :date")
                    ->setParameter('date', $date)
                    ->getQuery()
                    ->getSingleScalarResult();


                $count = $pulseCount + $weightCount + $saturationCount + $pressureCount;
                array_push($data, ['d' => strval($date), 'v' => strval($count)]);
            }
        }


        return $this->render('user/dashboard/chart.html.twig', [
            'data' => $data
        ]);
    }

}
