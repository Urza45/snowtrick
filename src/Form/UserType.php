<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('phone')
            ->add('cellPhone')
            ->add('salt', HiddenType::class)
            ->add('validationKey', HiddenType::class)
            ->add('activatedUser', HiddenType::class)
            ->add('isVerified', ChoiceType::class, [
                'choices'  => [
                    ' Non ' => false,
                    ' Oui ' => true
                ],
                'expanded' => true,
                'disabled' => 'disabled'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
