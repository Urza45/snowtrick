<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', RepeatedType::class, [
                'type' => TextType::class,
                'invalid_message' => "Les pseudos doivent être identiques.",
                /*'constraints' => [
                    new NotBlank(),
                    new Email()
                ],*/
                'required' => true,
                'first_options' => [
                    'label' => 'Saisir votre pseudo',
                    'attr' => [
                        'placeholder' => 'Votre pseudo',
                        'class' => 'form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre pseudo',
                    'attr' => [
                        'placeholder' => 'Votre pseudo',
                        'class' => 'form-control'
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => User::class
        ]);
    }
}
