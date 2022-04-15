<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'pseudo',
                TextType::class,
                [
                    'label' => 'Votre identifiant (*) :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre identifiant'
                    ],
                    'constraints' => [
                        new Length(
                            [
                                'min' => 5,
                                'minMessage' => 'Votre identifiant doit contenir au moins 5 caractères',
                                'max' => 20,
                                'maxMessage' => 'Votre identifiant doit contenir au plus 20 caractères.',
                            ]
                        ),
                    ]
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'label' => 'En m\'inscrivant, j\'accepte les CGU (*) ',
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'Vous devez accepter les Conditions Générales d\'Utilisation.',
                            ]
                        ),
                    ],
                ]
            )
            ->add('password', RepeatedPasswordType::class)
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Votre nom (*) :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre nom'
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Votre prénom (*) :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre prénom'
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Votre email (*) :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre email'
                    ],
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Votre téléphone :',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre téléphone'
                    ],
                ]
            )
            ->add(
                'cellPhone',
                TextType::class,
                [
                    'label' => 'Votre cellulaire :',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Votre cellulaire'
                    ],
                ]
            )
            ->add('salt', HiddenType::class)
            ->add('validationKey', HiddenType::class)
            ->add('activatedUser', HiddenType::class);
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
