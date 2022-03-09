<?php

namespace App\Entity;

use App\Repository\TypeMediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeMediaRepository::class)
 */
class TypeMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupMedia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeMedia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupMedia(): ?string
    {
        return $this->groupMedia;
    }

    public function setGroupMedia(string $groupMedia): self
    {
        $this->groupMedia = $groupMedia;

        return $this;
    }

    public function getTypeMedia(): ?string
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(string $typeMedia): self
    {
        $this->typeMedia = $typeMedia;

        return $this;
    }
}
