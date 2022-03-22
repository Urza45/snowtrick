<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BanUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('pseudo')
            // ->add('roles')
            // ->add('password')
            // ->add('lastName')
            // ->add('firstName')
            // ->add('email')
            // ->add('phone')
            // ->add('cellPhone')
            // ->add('salt')
            // ->add('statusConnected')
            // ->add('validationKey')
            ->add('activatedUser', ChoiceType::class, [
                'choices'  => [
                    '_Peut-être_' => null,
                    ' Non ' => true,
                    ' Oui ' => false,
                ],
                'expanded' => true,
                'label' => 'Etat actuel :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            // ->add('createdAt')
            // ->add('isVerified')
            // ->add('slug')
            // ->add('typeUser')
            // ->add('avatar')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
