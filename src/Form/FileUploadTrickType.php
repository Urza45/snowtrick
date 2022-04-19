<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FileUploadTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('legend')
            //->add('url')
            ->add(
                'url',
                FileType::class,
                [
                    //'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'mapped' => false, // Tell that there is no Entity to link
                    'required' => true,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '2M',
                                'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }}'
                                    . ' {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                                'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                                    'image/jpeg',
                                    'image/png'
                                ],
                                'mimeTypesMessage' => "Le type de votre photographie n'est pas valide.",
                            ]
                        )
                    ],
                ]
            )
            ->add(
                'featurePicture',
                ChoiceType::class,
                [
                    'choices'  => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'data' => false,
                    'expanded' => true,
                    'label' => 'Photographie mise en avant :',
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
                // 'data_class' => Media::class,
            ]
        );
    }
}
