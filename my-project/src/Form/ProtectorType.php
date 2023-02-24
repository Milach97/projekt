<?php

namespace App\Form;

use App\Entity\Protector;
use App\Entity\Protege;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProtectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('last_name', TextType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('phone_number', TelType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'mapped' => false
            ])
            ->add('protege', EntityType::class, [
                'class' => Protege::class,
                'required' => false,
                'choice_label' => function ($protege) {
                    return $protege->getUser()->getName().' '.$protege->getUser()->getLastName();
                },
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Protector::class,
        ]);
    }
}
