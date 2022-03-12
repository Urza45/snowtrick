<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            //->add('roles')
            //->add('password', PasswordType::class)
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('phone')
            ->add('cellPhone')
            ->add('salt', HiddenType::class)
            //->add('statusConnected', HiddenType::class)
            ->add('validationKey', HiddenType::class)
            ->add('activatedUser', HiddenType::class)
            //->add('createdAt', HiddenType::class)
            ->add('isVerified', HiddenType::class);
        //->add('typeUser', TypeUserType::class)
        //->add('avatar', AvatarType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
