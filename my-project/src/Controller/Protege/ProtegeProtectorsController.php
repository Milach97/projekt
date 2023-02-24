<?php

namespace App\Controller\Protege;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Protege;


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

        $proteges = $em->getRepository(Protege::class)->findOneBy(array('user' => $this->getUser()));
        if(!$proteges)
            throw $this->createNotFoundException('Protege not found. '.'User ID: '.$this->getUser()->getId());



        return $this->render('user/protege/protectors.html.twig', [
            'menuHighlight' => 'protectors',
            'proteges' => $proteges,
        ]);
    }


}
