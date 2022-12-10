<?php

namespace App\Service;

use App\Entity\Data\Pulse;
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


}
    