<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ModifyMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'legend',
                TextType::class,
                [
                    'label' => 'Légende :',
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
            //->add('url')
            ->add(
                'featurePicture',
                ChoiceType::class,
                [
                    'choices'  => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    //'data' => false,
                    'expanded' => true,
                    'label' => 'Photo mise en avant ?',
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
            //->add('thumbUrl')
            //->add('typeMedia')
            //->add('trick')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Media::class,
            ]
        );
    }
}
