<?php

namespace App\Entity;

use App\Repository\TypeMediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="typeMedia")
     */
    private $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setTypeMedia($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getTypeMedia() === $this) {
                $medium->setTypeMedia(null);
            }
        }

        return $this;
    }
}
