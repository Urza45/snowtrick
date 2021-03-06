<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add(
                'category',
                EntityType::class,
                [
                    'label' => 'Groupe de la figure : ',
                    // looks for choices from this entity
                    'class' => Category::class,
                    // uses the User.username property as the visible option string
                    'choice_label' => 'label',
                    'choice_value' => 'id'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Trick::class,
            ]
        );
    }
}
