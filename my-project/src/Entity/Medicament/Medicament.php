<?php

namespace App\Entity\Medicament;

use App\Repository\Medicament\MedicamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicamentRepository::class)]
class Medicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column]
    private ?bool $isChronic = null;

    #[ORM\OneToMany(mappedBy: 'medicament', targetEntity: MedicamentSchedule::class)]
    private Collection $medicamentSchedules;

    public function __construct()
    {
        $this->medicamentSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function isIsChronic(): ?bool
    {
        return $this->isChronic;
    }

    public function setIsChronic(bool $isChronic): self
    {
        $this->isChronic = $isChronic;

        return $this;
    }

    /**
     * @return Collection<int, MedicamentSchedule>
     */
    public function getMedicamentSchedules(): Collection
    {
        return $this->medicamentSchedules;
    }

    public function addMedicamentSchedule(MedicamentSchedule $medicamentSchedule): self
    {
        if (!$this->medicamentSchedules->contains($medicamentSchedule)) {
            $this->medicamentSchedules->add($medicamentSchedule);
            $medicamentSchedule->setMedicament($this);
        }

        return $this;
    }

    public function removeMedicamentSchedule(MedicamentSchedule $medicamentSchedule): self
    {
        if ($this->medicamentSchedules->removeElement($medicamentSchedule)) {
            // set the owning side to null (unless already changed)
            if ($medicamentSchedule->getMedicament() === $this) {
                $medicamentSchedule->setMedicament(null);
            }
        }

        return $this;
    }
}
