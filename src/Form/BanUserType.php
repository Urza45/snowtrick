<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BanUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'activatedUser',
                ChoiceType::class,
                [
                'choices'  => [
                    ' Peut-être ' => null,
                    ' Non ' => true,
                    ' Oui ' => false,
                ],
                'expanded' => true,
                'label' => 'Etat actuel :',
                'attr' => [
                    'class' => 'form-control',
                ]
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
