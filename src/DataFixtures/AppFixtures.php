<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TypeUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Type User
        $labelsTypeUser = ['Visiteur', 'Utilisateur enregistré', 'Administrateur'];

        foreach ($labelsTypeUser as $labelTypeUser) {
            $typeUser = new TypeUser();
            $typeUser->setLabel($labelTypeUser);

            $manager->persist($typeUser);
        }

        // Avatar
        $avatar = new Avatar();
        $avatar->setLegend('Femme')
            ->setType('jpg')
            ->setUrl('media\avatars\womanProfil.jpg');

        $manager->persist($avatar);

        $avatar = new Avatar();
        $avatar->setLegend('Homme')
            ->setType('jpg')
            ->setUrl('medias\avatars\manProfil.jpg');

        $manager->persist($avatar);

        // Admin User
        $date = new \DateTime();
        $date->setDate(2022, 2, 3);

        $userAdmin = new User();
        $userAdmin->setLastName('Doe')
            ->setFirstName('John')
            ->setPseudo('Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('mon@email.fr')
            ->setPhone('')
            ->setCellPhone('')
            ->setPassword('$2y$13$i/fr91Ni.lzPnuYIIXh5Leq1R./GBxQm14U.D0ePsOwhJvUT0Ze..')
            ->setSalt('')
            ->setStatusConnected(false)
            ->setActivatedUser(true)
            ->setValidationKey('-------------------------')
            ->setTypeUser($typeUser)
            ->setCreatedAt($date)
            ->setAvatar($avatar)
            ->setIsVerified(true);

        $manager->persist($userAdmin);

        // Category
        $categoriesNames = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];

        foreach ($categoriesNames as $categoryName) {
            $category = new Category();
            $category->setLabel($categoryName);

            $manager->persist($category);

            // Trick
            $trick = new Trick();
            $trick->setTitle('Un exemple de figure de ' . $category->getLabel());
            $trick->setChapo('Une courte description de cette figure de  snowboard libre.');
            $trick->setContent('Ici sera la description détaillée');
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);
            $trick->setCategory($category);
            $trick->setUser($userAdmin);

            $manager->persist($trick);

            // Add 4-6 comments
            $now = new \DateTime('NOW');

            for ($i = 1; $i <= mt_rand(4, 6); $i++) {
                $comment = new Comment();
                $comment->setContent('Voici le commentaire n°' . $i)
                    ->setCreatedAt($now)
                    ->setUpdatedAt($now)
                    ->setDisabled(false)
                    ->setNew(false)
                    ->setUser($userAdmin)
                    ->setTrick($trick);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
