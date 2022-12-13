<?php

namespace App\Service;

use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;
use App\Entity\Data\Pressure;
use App\Entity\Protege;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Protege
 *
 */
class ProtegeService {

    private $em;


    function __construct(EntityManagerInterface $entityManager){
        $this->em = $entityManager;
    }


    function savePulseAction(Protege $protege, int $pulseValue, $dateTime = null){
        $em = $this->em;

        //nowy zapis pulsu
        $pulse = new Pulse();
        $pulse->setValue($pulseValue);
        if($dateTime)
            $pulse->setDatetime($dateTime);
        else
            $pulse->setDatetime(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $pulse->setProtege($protege);
        $em->persist($pulse);

        //dodanie do podopiecznego
        $protege->addPulse($pulse);
        $em->persist($protege);
        $em->flush();
        

        return [
            'code' => 200,
            'message' => 'Nowa wartość pulsu została zapisana.'
        ];
    }


    function saveSaturationAction(Protege $protege, float $saturationValue, $dateTime = null){
        $em = $this->em;

        //nowy zapis pulsu
        $saturation = new Saturation();
        $saturation->setValue($saturationValue);
        if($dateTime)
            $saturation->setDatetime($dateTime);
        else
            $saturation->setDatetime(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $saturation->setProtege($protege);
        $em->persist($saturation);

        //dodanie do podopiecznego
        $protege->addSaturation($saturation);
        $em->persist($protege);
        $em->flush();
        

        return [
            'code' => 200,
            'message' => 'Nowa wartość saturacji została zapisana.'
        ];
    }



    function saveWeightAction(Protege $protege, float $weightValue, $dateTime = null){
        $em = $this->em;

        //nowy zapis pulsu
        $weight = new Weight();
        $weight->setValue($weightValue);
        if($dateTime)
            $weight->setDatetime($dateTime);
        else
            $weight->setDatetime(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $weight->setProtege($protege);
        $em->persist($weight);

        //dodanie do podopiecznego
        $protege->addWeight($weight);
        $em->persist($protege);
        $em->flush();
        

        return [
            'code' => 200,
            'message' => 'Nowa wartość wagi została zapisana.'
        ];
    }



    function savePressureAction(Protege $protege, float $systolicPressure, float $diastolicPressure, $dateTime = null){
        $em = $this->em;

        //nowy zapis pulsu
        $pressure = new Pressure();
        $pressure->setSystolicPressure($systolicPressure);
        $pressure->setDiastolicPressure($diastolicPressure);
        if($dateTime)
            $pressure->setDatetime($dateTime);
        else
            $pressure->setDatetime(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $pressure->setProtege($protege);
        $em->persist($pressure);

        //dodanie do podopiecznego
        $protege->addPressure($pressure);
        $em->persist($protege);
        $em->flush();
        

        return [
            'code' => 200,
            'message' => 'Nowa wartość ciśnienia została zapisana.'
        ];
    }
}
    