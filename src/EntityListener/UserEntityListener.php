<?php

namespace App\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserEntityListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $user->computeSlug($this->slugger);
    }

    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
        $user->computeSlug($this->slugger);
    }
}
