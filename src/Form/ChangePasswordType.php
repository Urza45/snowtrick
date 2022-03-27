<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Votre mot de passe actuel :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe actuel'
                ]
            ])
            ->add('newPassword', RepeatedPasswordType::class)
            ->add('send', SubmitType::class); // We could have added it in the view, as stated in the framework recommendations
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
