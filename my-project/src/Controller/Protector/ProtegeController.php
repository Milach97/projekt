<?php

namespace App\Controller\Protector;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Protege;

use App\Entity\Data\Pressure;
use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;

/**
 * @Security("is_granted('ROLE_PROTECTOR')")
 * @Route("/opiekun/podopieczni")
 */
class ProtegeController extends AbstractController
{


    /**
     * @Route("/", name="protector_proteges")
     * @Route("/lista", name="protector_proteges_list")
     */
    public function listAction(Request $request, EntityManagerInterface $em)
    {
        //znajdz wszystkich podopiecznych opiekuna
        $proteges = $this->getUser()->getProtector()->getProtege();


        return $this->render('user/protector/proteges/list.html.twig', [
            'menuHighlight' => 'proteges',
            'proteges' => $proteges
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}", name="protector_proteges_manage")
     */
    public function manageAction(Request $request, EntityManagerInterface $em, $id){
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);
      
        if(!$this->getUser()->getProtector()->getProtege()->contains($protege))
            throw $this->createNotFoundException('Not allowed. ');

        //pobierz ostatnie 10 nanjowszych zapisow cisnienia
        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            
        
        //pobierz ostatnie 10 nanjowszych zapisow pulsu
        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnie 10 nanjowszych zapisow saturacji
        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnia wpisana waga podopiecznego
        $protegeWeight = $em->getRepository(Weight::class)->findOneBy(['protege' => $protege], ['datetime' => 'DESC']);

        return $this->render('user/protector/proteges/manage.html.twig', [
            'menuHighlight' => 'proteges',
            'protege' => $protege,
            'protegePressureRecords' => $protegePressureRecords,
            'protegePulsRecords' => $protegePulsRecords,
            'protegeSaturationRecords' => $protegeSaturationRecords,
            'protegeWeight' => $protegeWeight

        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/puls", name="protector_proteges_manage_data_pulse")
     */
    public function dataPulseAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protector/proteges/data/pulse.html.twig', [
            'menuHighlight' => 'proteges',
            'protegePulsRecords' => $protegePulsRecords
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/saturacja", name="protector_proteges_manage_data_saturation")
     */
    public function dataSaturationAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protector/proteges/data/saturation.html.twig', [
            'menuHighlight' => 'proteges',
            'protegeSaturationRecords' => $protegeSaturationRecords
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/cisnienie", name="protector_proteges_manage_data_pressure")
     */
    public function dataPressureAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protector/proteges/data/pressure.html.twig', [
            'menuHighlight' => 'proteges',
            'protegePressureRecords' => $protegePressureRecords
        ]);
    }
}