<?php

namespace App\Controller\Protege;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\Data\PulseType;
use App\Form\Data\SaturationType;
use App\Form\Data\PressureType;
use App\Form\Data\WeightType;
use App\Service\ProtegeService;

use App\Entity\User;
use App\Entity\Protege;
use App\Entity\Data\Disease;
use App\Entity\Data\Pressure;
use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;


/**
 * @Security("is_granted('ROLE_PROTEGE')")
 * @Route("/p/zdrowie")
 */
class ProtegeHealthController extends AbstractController
{
    /**
     * @Route("/", name="protege_health")
     */
    public function protegeHealth(Request $request, EntityManagerInterface $em)
    {
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



    /**
     * @Route("/puls/dodaj", name="protege_health_pulse_add")
     */
    public function protegeHealthPulseAdd(Request $request, EntityManagerInterface $em)
    {
        //formularz 
        $form = $this->createForm(PulseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //user z formularza
            $pulse = $form->getData();

            //serwis
            $s = new ProtegeService($em);
            $response = $s->savePulseAction($this->getUser()->getProtege(), $pulse->getValue(), $pulse->getDatetime());

            $this->addFlash('success', $response['message']);
            return $this->redirectToRoute('protege_health');
        }


        return $this->render('user/protege/pulse/add.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/saturacja/dodaj", name="protege_health_saturation_add")
     */
    public function protegeHealthSaturationAdd(Request $request, EntityManagerInterface $em)
    {
        //formularz 
        $form = $this->createForm(SaturationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //user z formularza
            $saturation = $form->getData();

            //serwis
            $s = new ProtegeService($em);
            $response = $s->saveSaturationAction($this->getUser()->getProtege(), $saturation->getValue(), $saturation->getDatetime());

            $this->addFlash('success', $response['message']);
            return $this->redirectToRoute('protege_health');
        }


        return $this->render('user/protege/saturation/add.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'form' => $form->createView()
        ]);
    }

    

    /**
     * @Route("/waga/dodaj", name="protege_health_weight_add")
     */
    public function protegeHealthWeightAdd(Request $request, EntityManagerInterface $em)
    {
        //formularz 
        $form = $this->createForm(WeightType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //user z formularza
            $weight = $form->getData();

            //serwis
            $s = new ProtegeService($em);
            $response = $s->saveWeightAction($this->getUser()->getProtege(), $weight->getValue(), $weight->getDatetime());

            $this->addFlash('success', $response['message']);
            return $this->redirectToRoute('protege_health');
        }


        return $this->render('user/protege/weight/add.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/cisnienie/dodaj", name="protege_health_pressure_add")
     */
    public function protegeHealthPressureAdd(Request $request, EntityManagerInterface $em)
    {
        //formularz 
        $form = $this->createForm(PressureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //user z formularza
            $pressure = $form->getData();

            //serwis
            $s = new ProtegeService($em);
            $response = $s->savePressureAction($this->getUser()->getProtege(), $pressure->getSystolicPressure(), $pressure->getDiastolicPressure(), $pressure->getDatetime());

            $this->addFlash('success', $response['message']);
            return $this->redirectToRoute('protege_health');
        }


        return $this->render('user/protege/pressure/add.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/puls/dane", name="protege_health_pulse_data")
     */
    public function dataPulseAction(Request $request, EntityManagerInterface $em)
    {
        $protege = $em->getRepository(Protege::class)->findOneBy(array('user' => $this->getUser()));
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'User ID: '.$this->getUser()->getId());


        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protege/pulse/pulse.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'protegePulsRecords' => $protegePulsRecords
        ]);
    }


    /**
     * @Route("/saturacja/dane", name="protege_health_saturation_data")
     */
    public function dataSaturationAction(Request $request, EntityManagerInterface $em)
    {
        $protege = $em->getRepository(Protege::class)->findOneBy(array('user' => $this->getUser()));
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'User ID: '.$this->getUser()->getId());


        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protege/saturation/saturation.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'protegeSaturationRecords' => $protegeSaturationRecords
        ]);
    }


    /**
     * @Route("/cisnienie/dane", name="protege_health_pressure_data")
     */
    public function dataPressureAction(Request $request, EntityManagerInterface $em)
    {
        $protege = $em->getRepository(Protege::class)->findOneBy(array('user' => $this->getUser()));
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'User ID: '.$this->getUser()->getId());


        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/protege/pressure/pressure.html.twig', [
            'menuHighlight' => 'protegeHealth',
            'protegePressureRecords' => $protegePressureRecords
        ]);
    }
}
