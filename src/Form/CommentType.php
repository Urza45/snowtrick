<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire :',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('createdAt')
            // ->add('disabled')
            // ->add('new')
            // ->add('updatedAt')
            // ->add('user')
            // ->add('trick')
            ->add('captcha', IntegerType::class, [
                'label' => '',
                'invalid_message' => 'Vous devez saisir un nombre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Captcha *'
                ],
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 4,
                        'exactMessage' => 'Votre captcha doit contenir exactement 4 caractères.',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
