<?php

namespace App\Controller\Protege;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Protege;
use App\Entity\Data\Disease;
use App\Entity\Data\Pressure;
use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;


/**
 * @Security("is_granted('ROLE_PROTEGE')")
 * @Route("/p/opiekun")
 */
class ProtegeProtectorsController extends AbstractController
{
    /**
     * @Route("/", name="protege_protectors")
     */
    public function protegeCard(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_PROTEGE');
        //TODO: dodac protege i protecor do USER


        $protege = $em->getRepository(Protege::class)->findOneBy(array('user' => $this->getUser()));
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'User ID: '.$this->getUser()->getId());


        //pobierz ostatnie 10 nanjowszych zapisanych chorob
        $protegeDiseaseRecords = $em->getRepository(Disease::class)->findBy(['protege' => $protege], ['isChronic' => 'ASC', 'startDate' => 'DESC'], 10);            
        
        //pobierz ostatnie 10 nanjowszych zapisow cisnienia
        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            
        
        //pobierz ostatnie 10 nanjowszych zapisow pulsu
        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnie 10 nanjowszych zapisow saturacji
        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnia wpisana waga podopiecznego
        $protegeWeight = $em->getRepository(Weight::class)->findOneBy(['protege' => $protege], ['datetime' => 'DESC']);


        return $this->render('user/protege/protegeHealth.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'protege' => $protege,
            'protegeDiseaseRecords' => $protegeDiseaseRecords,
            'protegePressureRecords' => $protegePressureRecords,
            'protegePulsRecords' => $protegePulsRecords,
            'protegeSaturationRecords' => $protegeSaturationRecords,
            'protegeWeight' => $protegeWeight
        ]);
    }


}
