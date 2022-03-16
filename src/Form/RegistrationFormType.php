<?php

namespace App\Form;

use App\Entity\User;
use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Votre identifiant :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre identifiant'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre identifiant doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'maxMessage' => 'Votre identifiant doit contenir au plus {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En m\'inscrivant, j\'accepte les CGU',
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatedPasswordType::class)
            /*
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Votre mot de passe :',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 10,
                        'maxMessage' => 'Votre mot de passe doit contenir au plus {{ limit }} caractères.',
                    ]),
                ],
            ])
            */
            ->add('lastName', TextType::class, [
                'label' => 'Votre nom :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom'
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Votre prénom :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre email'
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre téléphone :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre téléphone'
                ],
            ])
            ->add('cellPhone', TextType::class, [
                'label' => 'Votre cellulaire :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre cellulaire'
                ],
            ])
            ->add('salt', HiddenType::class)
            //->add('statusConnected', HiddenType::class)
            ->add('validationKey', HiddenType::class)
            ->add('activatedUser', HiddenType::class);
        //->add('createdAt', HiddenType::class)
        //->add('isVerified', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
