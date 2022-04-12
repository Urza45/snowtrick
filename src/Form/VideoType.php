<?php

namespace App\Form;

use App\Entity\TypeMedia;
use App\Repository\TypeMediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('legend')
            ->add('url')
            // ->add('typeMedia', EntityType::class, [
            //     // looks for choices from this entity
            //     'class' => TypeMedia::class,
            //     'query_builder' => function (TypeMediaRepository $er) {
            //         return $er->createQueryBuilder('u')
            //             ->andWhere('u.groupMedia = :groupMedia')
            //             ->setParameter('groupMedia', 'Vidéo')
            //             ->orderBy('u.typeMedia', 'ASC');
            //     },
            //     // uses the User.username property as the visible option string
            //     'choice_label' => 'typeMedia',
            //     'attr' => [
            //         'class' => 'form-select',
            //     ]

            //     // used to render a select box, check boxes or radios
            //     // 'multiple' => true,
            //     // 'expanded' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            // Configure your form options here
            ]
        );
    }
}
