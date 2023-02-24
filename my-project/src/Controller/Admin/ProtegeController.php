<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Protege;
use App\Entity\Protector;
use App\Form\ProtegeType;

use App\Entity\Data\Pressure;
use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




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
     * @Route("/dodaj/",       name="admin_proteges_new")
     * @Route("/edytuj/{id}/", name="admin_proteges_edit")
     */
    public function editAction( Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, $id = null)
    {
        //jezeli istnieje -> znajdz i edytuj
        if($id) {
            $protege = $em->getRepository(Protege::class)->find($id);
            $arr = [
                'email' => $protege->getUser()->getEmail(),
                'name' => $protege->getUser()->getName(),
                'last_name' => $protege->getUser()->getLastName(),
                'phone_number' => $protege->getUser()->getPhoneNumber(),
                'protector' => $protege->getProtector(),
                'height' => $protege->getHeight(),
                'gender' => $protege->getGender()
            ];

            $formBuilder = $this->createFormBuilder($arr);
            $formBuilder
                ->add('email', EmailType::class, ['mapped' => false, 'data' => $arr['email']])
                ->add('name', TextType::class, ['mapped' => false, 'data' => $arr['name']])
                ->add('last_name', TextType::class, ['mapped' => false, 'data' => $arr['last_name']])
                ->add('gender', ChoiceType::class, [
                    'data' => $arr['gender'],
                    'required' => true,
                    'choices'  => [
                        'Kobieta' => 'FEMALE',
                        'Mężczyzna' => 'MALE'
                    ],
                ])
                ->add('height', NumberType::class, ['data' => $arr['height']])
                ->add('phone_number', TelType::class, ['mapped' => false, 'required' => false, 'data' => $arr['phone_number']])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => false,
                    'mapped' => false
                ])
                ->add('protector', EntityType::class, [
                    'class' => Protector::class,
                    'required' => false,
                    'multiple' => true,
                    'choice_label' => function ($protector) {
                        return $protector->getUser()->getName().' '.$protector->getUser()->getLastName();
                    }
                ]);
            $form = $formBuilder->getForm();
        }
        //innaczej -> stworz nowe
        else {
            $protege = new Protege();      
            $form = $this->createForm(ProtegeType::class);

        }

        //formularz
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            if($id){
                //istenijacy opiekun -> nadpisz dane
                $user = $protege->getUser();
                if($form->get("password")->getData()) 
                    $user->setPassword($passwordHasher->hashPassword($user, $form->get("password")->getData()));
            }
            else{
                //nowy uzytkownik
                $user = new User();
                $user->setPassword($passwordHasher->hashPassword($user, $form->get("password")->getData()));
                $user->setRoles(['ROLE_PROTEGE']);
                $user->setProtege($protege);
                $protege->setUser($user);
            }

            $user->setEmail($form->get("email")->getData());
            $user->setName($form->get("name")->getData());
            $user->setLastName($form->get("last_name")->getData());
            $user->setPhoneNumber($form->get("phone_number")->getData());

            $em->persist($user);
            $em->persist($protege);
            $em->flush();



            $this->addFlash('success', 'Podopieczny został zapisany pomyślnie.');
            return $this->redirectToRoute('admin_proteges');
        }

        return $this->render('user/admin/proteges/edit.html.twig', [
            'form' => $form->createView(),
            'protege' => $protege,
            'menuHighlight' => 'proteges'
        ]);
    }



    /**
     * @Route("/zarzadzaj/{id<\d+>}", name="admin_proteges_manage")
     */
    public function manageAction(Request $request, EntityManagerInterface $em, $id){
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        //pobierz ostatnie 10 nanjowszych zapisow cisnienia
        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            
        
        //pobierz ostatnie 10 nanjowszych zapisow pulsu
        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnie 10 nanjowszych zapisow saturacji
        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC'], 10);            

        //pobierz ostatnia wpisana waga podopiecznego
        $protegeWeight = $em->getRepository(Weight::class)->findOneBy(['protege' => $protege], ['datetime' => 'DESC']);

        return $this->render('user/admin/proteges/manage.html.twig', [
            'menuHighlight' => 'proteges',
            'protege' => $protege,
            'protegePressureRecords' => $protegePressureRecords,
            'protegePulsRecords' => $protegePulsRecords,
            'protegeSaturationRecords' => $protegeSaturationRecords,
            'protegeWeight' => $protegeWeight

        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/puls", name="admin_proteges_manage_data_pulse")
     */
    public function dataPulseAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegePulsRecords = $em->getRepository(Pulse::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/admin/proteges/data/pulse.html.twig', [
            'menuHighlight' => 'proteges',
            'protegePulsRecords' => $protegePulsRecords
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/saturacja", name="admin_proteges_manage_data_saturation")
     */
    public function dataSaturationAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegeSaturationRecords = $em->getRepository(Saturation::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/admin/proteges/data/saturation.html.twig', [
            'menuHighlight' => 'proteges',
            'protegeSaturationRecords' => $protegeSaturationRecords
        ]);
    }


    /**
     * @Route("/zarzadzaj/{id<\d+>}/dane/cisnienie", name="admin_proteges_manage_data_pressure")
     */
    public function dataPressureAction(Request $request, EntityManagerInterface $em, $id)
    {
        $protege = $em->getRepository(Protege::class)->find($id);
        if(!$protege)
            throw $this->createNotFoundException('Protege not found. '.'ID: '.$id);

        $protegePressureRecords = $em->getRepository(Pressure::class)->findBy(['protege' => $protege], ['datetime' => 'DESC']);    
        
        
        return $this->render('user/admin/proteges/data/pressure.html.twig', [
            'menuHighlight' => 'proteges',
            'protegePressureRecords' => $protegePressureRecords
        ]);
    }
}