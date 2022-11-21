<?php

namespace App\Entity\Data;

use App\Entity\Protege;
use App\Repository\Data\PressureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PressureRepository::class)]
class Pressure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pressures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Protege $protege = null;

    #[ORM\Column]
    private ?float $systolicPressure = null;

    #[ORM\Column]
    private ?float $diastolicPressure = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProtege(): ?Protege
    {
        return $this->protege;
    }

    public function setProtege(?Protege $protege): self
    {
        $this->protege = $protege;

        return $this;
    }

    public function getSystolicPressure(): ?float
    {
        return $this->systolicPressure;
    }

    public function setSystolicPressure(float $systolicPressure): self
    {
        $this->systolicPressure = $systolicPressure;

        return $this;
    }

    public function getDiastolicPressure(): ?float
    {
        return $this->diastolicPressure;
    }

    public function setDiastolicPressure(float $diastolicPressure): self
    {
        $this->diastolicPressure = $diastolicPressure;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
