<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\Protector;
use App\Form\ProtectorType;
use App\Entity\User;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin/opiekunowie")
 */
class ProtectorController extends AbstractController
{

    /**
     * @Route("/", name="admin_protectors")
     * @Route("/lista", name="admin_protectors_list")
     */
    public function listAction(Request $request, EntityManagerInterface $em)
    {
        $protectors = $em->getRepository(Protector::class)->findAll();
        //dd($protectors);

        return $this->render('user/admin/protectors/list.html.twig', [
            'protectors' => $protectors,
            'menuHighlight' => 'protectors'
        ]);
    }


    /**
     * @Route("/dodaj/",       name="admin_protectors_new")
     * @Route("/edytuj/{id}/", name="admin_protectors_edit")
     */
    public function editAction( Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, $id = null)
    {
        //jezeli istnieje -> znajdz i edytuj
        if($id) {
            $protector = $em->getRepository(Protector::class)->find($id);
            $arr = [
                'email' => $protector->getUser()->getEmail(),
                'name' => $protector->getUser()->getName(),
                'last_name' => $protector->getUser()->getLastName(),
            ];

            $formBuilder = $this->createFormBuilder($arr);
            $formBuilder
                ->add('email', EmailType::class, ['mapped' => false])
                ->add('name', TextType::class, ['mapped' => false])
                ->add('last_name', TextType::class, ['mapped' => false])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false
                ]);
            $form = $formBuilder->getForm();
        }
        //innaczej -> stworz nowe
        else {
            $protector = new Protector();      
            $form = $this->createForm(ProtectorType::class);

        }

        //formularz
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //serwis tworzenia uzytkownika?
            //TODO nowy i edycja - zmienic tworzenie uzytkownika

            $protector = $form->getData();

            //stworz uzytkownika
            $user = new User();
            $user->setName($form->get("name")->getData());
            $user->setLastName($form->get("last_name")->getData());
            $user->setEmail($form->get("email")->getData());
            $user->setPassword($passwordHasher->hashPassword($user, $form->get("password")->getData()));
            $user->setRoles(['ROLE_PROTECTOR']);
            $em->persist($user);
            $em->flush();

            $protector->setUser($user);
            $em->persist($protector);
            $em->flush();


            $this->addFlash('success', 'Dodanie nowego opiekuna przebiegło pomyślnie.');
            return $this->redirectToRoute('admin_protectors');
        }

        return $this->render('user/admin/protectors/edit.html.twig', [
            'form' => $form->createView(),
            'protector' => $protector,
            'menuHighlight' => 'protectors'
        ]);
    }

}