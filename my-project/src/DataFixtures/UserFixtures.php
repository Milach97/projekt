<?php

namespace App\DataFixtures;

use App\Entity\Data\Saturation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
#use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Protege;
use App\Entity\Protector;
use App\Entity\Data\Weight;
use App\Entity\Data\Pulse;
use Symfony\Component\Validator\Constraints\DateTime;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $roles, $protegeData])
        {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setRoles($roles);

            //jezeli dodawany jest podopieczny - stworz w bazie
            if(in_array("ROLE_PROTEGE", $roles)){
                $protege = new Protege();
                $protege->setUser($user);

                if($protegeData){
                    $protege->setGender($protegeData[0]);
                    $protege->setHeight($protegeData[1]);

                    //waga
                    foreach ($protegeData[2] as $weightArr){
                        $weight = new Weight();
                        $weight->setValue($weightArr[0]);
                        $weight->setDateTime(new \DateTime($weightArr[1]));
                        $manager->persist($weight);
                        $protege->addWeight($weight);
                    }

                    //puls
                    foreach ($protegeData[3] as $pulseArr){
                        $pulse = new Pulse();
                        $pulse->setValue($pulseArr[0]);
                        $pulse->setDateTime(new \DateTime($pulseArr[1]));
                        $manager->persist($pulse);
                        $protege->addPulse($pulse);
                    }

                    //saturacja
                    foreach ($protegeData[4] as $saturationArr){
                        $saturation = new Saturation();
                        $saturation->setValue($saturationArr[0]);
                        $saturation->setDateTime(new \DateTime($saturationArr[1]));
                        $manager->persist($saturation);
                        $protege->addSaturation($saturation);
                    }
                }


                $manager->persist($protege);
            }
            elseif (in_array('ROLE_PROTECTOR', $roles)){
                $protector = new Protector();
                $protector->setUser($user);
                $manager->persist($protector);
            }
        
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [

            ['Jan', 'Nowak', 'admin@sample.com', 'wojtek', ['ROLE_USER', 'ROLE_ADMIN'], []],
            ['Kacper', 'Malinowski', 'protector@sample.com', 'wojtek', ['ROLE_USER', 'ROLE_PROTECTOR'], []],
            ['Tadeusz', 'Kamiński', 'user@sample.com', 'wojtek', ['ROLE_USER', 'ROLE_PROTEGE'], $this->getProtegeData(1)],
            ['Kamil', 'Tarczyński', 'user1@sample.com', 'wojtek', ['ROLE_USER', 'ROLE_PROTEGE'],  $this->getProtegeData(2)],
            ['Małgorzata', 'Wojciechowska', 'user2@sample.com', 'wojtek', ['ROLE_USER', 'ROLE_PROTEGE'],  $this->getProtegeData(3)]

        ];
    }

    private function getProtegeData($select): array
    {
        //TODO + set protector
        //gender, height, [weights], [pulses], [saturations]


        if($select == 1){

            return ['MALE', 180, 
                    [
                        [80, '2022-06-13 13:13'], 
                        [85, '2022-09-13 17:10']
                    ],
    
                    [
                        [90, '2022-06-13 13:13']
                    ], 
    
                    [
                        [85, '2022-09-13 17:10']
                    ]
                ];
        } elseif($select == 2){
            
            return ['MALE', 162, 
                    [
                        [70, '2022-06-13 13:13'], 
                        [68, '2022-09-13 17:10']
                    ],
    
                    [
                        [90, '2022-06-13 13:13']
                    ], 
    
                    [
                        [85, '2022-09-13 17:10']
                    ]
                ];
        
        } elseif($select == 3){

            return ['FEMALE', 166, 
                    [
                        [62, '2022-06-13 13:13'], 
                        [60, '2022-09-13 17:10']
                    ],
    
                    [
                        [77, '2022-06-13 13:13']
                    ], 
    
                    [
                        [95, '2022-09-13 17:10']
                    ]
                ];
        
        }
        else{
            return [];
        }
    }


}

