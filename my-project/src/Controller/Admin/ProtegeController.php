<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Protege;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin/podopieczni")
 */
class ProtegeController extends AbstractController
{


    /**
     * @Route("/", name="admin_proteges")
     * @Route("/lista", name="admin_proteges_list")
     */
    public function listAction(Request $request, EntityManagerInterface $em)
    {
        $proteges = $em->getRepository(Protege::class)->findAll();


        return $this->render('user/admin/proteges/list.html.twig', [
            'menuHighlight' => 'proteges',
            'proteges' => $proteges
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}", name="admin_proteges_manage")
     */
    public function manageAction(Request $request, EntityManagerInterface $em, $id){
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);
        

        return $this->render('user/admin/proteges/manage.html.twig', [
            'menuHighlight' => 'proteges',
            'protege' => $protege
        ]);
    }

}