<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\User\UserEditType;


/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin/uzytkownicy")
 */
class UserController extends AbstractController
{


    /**
     * @Route("/", name="admin_users")
     * @Route("/lista", name="admin_users_list")
     */
    public function listAction(Request $request, EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('user/admin/users/list.html.twig', [
            'menuHighlight' => 'users',
            'users' => $users
        ]);
    }



    /**
     * @Route("/edytuj/{id<\d+>}/", name="admin_users_edit")
     */
    public function editAction( Request $request, EntityManagerInterface $em, $id)
    {
        $user = $em->getRepository(User::class)->find($id);
        if(!$user)
            throw $this->createNotFoundException('User not found. ID: '.$id);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //user z formularza
            $user = $form->getData();

            //jezeli zmieniono haslo
            if($form['plainPassword']->getData()) {
                //zakoduj haslo i wprowadz do bazy przez service?
                //$encoded = $encoder->encodePassword($user, $data->getPlainPassword());
                
                //$user->setPassword($form['plainPassword']->getData());
            }


            $em->persist($user);
            $em->flush();


            $this->addFlash('success', 'Pomyślnie dokonano edycji użytkownika.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/admin/users/edit.html.twig', [
            'menuHighlight' => 'users',
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}