<?php

namespace App\DataFixtures;

use App\Entity\TypeMedia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeMediaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typeMedia = new TypeMedia();
        $typeMedia->setGroupMedia('Vidéo')
            ->setTypeMedia('mp4');

        $manager->persist($typeMedia);

        $typeMedia = new TypeMedia();
        $typeMedia->setGroupMedia('Image')
            ->setTypeMedia('jpg');

        $manager->persist($typeMedia);

        $typeMedia = new TypeMedia();
        $typeMedia->setGroupMedia('Image')
            ->setTypeMedia('png');

        $manager->persist($typeMedia);

        $typeMedia = new TypeMedia();
        $typeMedia->setGroupMedia('Image')
            ->setTypeMedia('gif');

        $manager->persist($typeMedia);

        $manager->flush();
    }
}
