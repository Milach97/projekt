<?php

namespace App\Controller\User;

use App\Service\UserService;
use App\Form\User\PasswordType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/konto")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="user_account")
     */
    public function account(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //info o koncie
        $user = $this->getUser();

        return $this->render('user/account.html.twig', [
            'menuHighlight' => 'account',
            'user' => $user
        ]);
    }



    /**
     * @Route("/haslo", name="user_account_passwordChange")
     */
    public function accountPasswordChange(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getUser();

        //formularz do FORM
        $form = $this->createForm(PasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $arr = $form->getData();

            //sprawdzenie czy stare haslo jest zgodne
            if (!$passwordHasher->isPasswordValid($user, $arr['oldPassword'])) {
                $this->addFlash('error', 'Błędnie wprowadzono stare hasło.');
                return $this->redirectToRoute('user_account_passwordChange');
            }
            
            //sprawdzenie zgodnosci nowego hasla z potwierdzeniem
            if($arr['newPassword'] != $arr['newPasswordConfirm']){
                $this->addFlash('error', 'Nowe hasło jest niezgodne z powtórzeniem nowego hasła.');
                return $this->redirectToRoute('user_account_passwordChange');
            }

            //sprawdzenie czy nowe haslo nie jest puste
            if($arr['newPassword'] == ''){
                $this->addFlash('error', 'Nowe hasało nie zostało uzupelnione.');
                return $this->redirectToRoute('user_account_passwordChange');
            }

            //sprawdzenie dlugosci podanego hasla
            if (strlen($arr['newPassword']) < 8) {
                $this->addFlash('error', 'Nowe hasło jest zbyt krótkie. Upewnij się, że podane hasło nie jest krótsze niż 8 znaków.');
                return $this->redirectToRoute('user_account_passwordChange');
            }

            //sprawdzenie czy haslo zawiera cyfre
            if (!preg_match("#[0-9]+#", $arr['newPassword'])) {
                $this->addFlash('error', 'Hasło powinno zawierać co najmniej jedną cyfrę.');
                return $this->redirectToRoute('user_account_passwordChange');
            }

            //sprawdzenie czy haslo zawiera litere
            if (!preg_match("#[a-zA-Z]+#", $arr['newPassword'])) {
                $this->addFlash('error', 'Hasło powinno zawierać co najmniej jedną literę.');
                return $this->redirectToRoute('user_account_passwordChange');
            }     


            //jezeli wszystko OK -> serwis zmiany hasla

            $us = new UserService($em, $passwordHasher);
            $resp = $us->changePasswordAction($user, $arr['newPassword']);


            if(array_key_exists('code', $resp)){
                //OK
                if($resp['code'] == 200){
                    $this->addFlash('succes', $resp['message']);
                    
                }
                //BLAD
                else{
                    $this->addFlash('error', 'Błąd przy próbie zmiany hasła. Jeżeli problem się powtarza, skontaktuj się z administratorem.');
                }
            }
            else{
                //blad odpowiedzi
                $this->addFlash('error', 'Błąd przy próbie zmiany hasła. Jeżeli problem się powtarza, skontaktuj się z administratorem.');
            }

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/accountPasswordChange.html.twig', [
            'form' => $form->createView(),
            'error'=> ''
        ]);
    }

}
