<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index()
    {
        // jezeli potrzebna bedzie funkcjonalnosc nadawania uprawnien
        // if(!$this->getUser()->hasPrivilegeByRoute($request->get('_route')))
        //     $this->denyAccessUnlessGranted($request);
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/dashboard.html.twig', [
            'menuHighlight' => 'dashboard'
        ]);
    }


}
