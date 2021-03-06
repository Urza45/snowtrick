<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ShowUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => [
                        'Admininistrateur' => 'ROLE_ADMIN',
                        'Utilisateur enregistré' => 'ROLE_USER',
                    ],
                    'expanded'  => false, // liste déroulante
                    'multiple'  => true, // choix multiple
                ]
            )

            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('phone')
            ->add('cellPhone')
            ->add('createdAt', DateType::class)
            ->add(
                'isVerified',
                ChoiceType::class,
                [
                    'choices'  => [
                        ' Non ' => false,
                        ' Oui ' => true
                    ],
                    'expanded' => true,
                    'disabled' => 'disabled'
                ]
            )
            ->add(
                'slug',
                TextType::class,
                [
                    'disabled' => 'disabled'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
