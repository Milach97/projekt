<?php

namespace App\Entity;

use App\Repository\ProtectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtectorRepository::class)]
class Protector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Protege::class, inversedBy: 'protector')]
    private Collection $protege;

    public function __construct()
    {
        $this->protege = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Protege>
     */
    public function getProtege(): Collection
    {
        return $this->protege;
    }

    public function addProtege(Protege $protege): self
    {
        if (!$this->protege->contains($protege)) {
            $this->protege->add($protege);
        }

        return $this;
    }

    public function removeProtege(Protege $protege): self
    {
        $this->protege->removeElement($protege);

        return $this;
    }
}
