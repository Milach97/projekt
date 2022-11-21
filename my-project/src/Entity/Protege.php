<?php

namespace App\Entity;

use App\Entity\Data\Pressure;
use App\Entity\Data\Pulse;
use App\Entity\Data\Saturation;
use App\Entity\Data\Weight;
use App\Repository\ProtegeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtegeRepository::class)]
class Protege
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'protege', targetEntity: Pulse::class, orphanRemoval: true)]
    private Collection $pulses;

    #[ORM\OneToMany(mappedBy: 'protege', targetEntity: Saturation::class, orphanRemoval: true)]
    private Collection $saturations;

    //TODO stworzyc enum
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    private ?float $height = null;

    #[ORM\OneToMany(mappedBy: 'protege', targetEntity: Weight::class, orphanRemoval: true)]
    private Collection $weights;

    #[ORM\OneToMany(mappedBy: 'protege', targetEntity: Pressure::class, orphanRemoval: true)]
    private Collection $pressures;


    public function __construct()
    {
        $this->pulses = new ArrayCollection();
        $this->saturations = new ArrayCollection();
        $this->weights = new ArrayCollection();
        $this->pressures = new ArrayCollection();
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
     * @return Collection<int, Pulse>
     */
    public function getPulses(): Collection
    {
        return $this->pulses;
    }

    public function addPulse(Pulse $pulse): self
    {
        if (!$this->pulses->contains($pulse)) {
            $this->pulses->add($pulse);
            $pulse->setProtege($this);
        }

        return $this;
    }

    public function removePulse(Pulse $pulse): self
    {
        if ($this->pulses->removeElement($pulse)) {
            // set the owning side to null (unless already changed)
            if ($pulse->getProtege() === $this) {
                $pulse->setProtege(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Saturation>
     */
    public function getSaturations(): Collection
    {
        return $this->saturations;
    }

    public function addSaturation(Saturation $saturation): self
    {
        if (!$this->saturations->contains($saturation)) {
            $this->saturations->add($saturation);
            $saturation->setProtege($this);
        }

        return $this;
    }

    public function removeSaturation(Saturation $saturation): self
    {
        if ($this->saturations->removeElement($saturation)) {
            // set the owning side to null (unless already changed)
            if ($saturation->getProtege() === $this) {
                $saturation->setProtege(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return Collection<int, Weight>
     */
    public function getWeights(): Collection
    {
        return $this->weights;
    }

    public function addWeight(Weight $weight): self
    {
        if (!$this->weights->contains($weight)) {
            $this->weights->add($weight);
            $weight->setProtege($this);
        }

        return $this;
    }

    public function removeWeight(Weight $weight): self
    {
        if ($this->weights->removeElement($weight)) {
            // set the owning side to null (unless already changed)
            if ($weight->getProtege() === $this) {
                $weight->setProtege(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pressure>
     */
    public function getPressures(): Collection
    {
        return $this->pressures;
    }

    public function addPressure(Pressure $pressure): self
    {
        if (!$this->pressures->contains($pressure)) {
            $this->pressures->add($pressure);
            $pressure->setProtege($this);
        }

        return $this;
    }

    public function removePressure(Pressure $pressure): self
    {
        if ($this->pressures->removeElement($pressure)) {
            // set the owning side to null (unless already changed)
            if ($pressure->getProtege() === $this) {
                $pressure->setProtege(null);
            }
        }

        return $this;
    }
    
}
