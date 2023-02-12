<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomAcitivite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionActivite = null;

    #[ORM\Column(length: 255)]
    private ?string $dureeActivite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateActivite = null;

    #[ORM\Column(length: 255)]
    private ?string $coach = null;

    #[ORM\Column]
    private ?int $nbrePlace = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAcitivite(): ?string
    {
        return $this->nomAcitivite;
    }

    public function setNomAcitivite(string $nomAcitivite): self
    {
        $this->nomAcitivite = $nomAcitivite;

        return $this;
    }

    public function getDescriptionActivite(): ?string
    {
        return $this->descriptionActivite;
    }

    public function setDescriptionActivite(string $descriptionActivite): self
    {
        $this->descriptionActivite = $descriptionActivite;

        return $this;
    }

    public function getDureeActivite(): ?string
    {
        return $this->dureeActivite;
    }

    public function setDureeActivite(string $dureeActivite): self
    {
        $this->dureeActivite = $dureeActivite;

        return $this;
    }

    public function getDateActivite(): ?\DateTimeInterface
    {
        return $this->DateActivite;
    }

    public function setDateActivite(\DateTimeInterface $DateActivite): self
    {
        $this->DateActivite = $DateActivite;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getNbrePlace(): ?int
    {
        return $this->nbrePlace;
    }

    public function setNbrePlace(int $nbrePlace): self
    {
        $this->nbrePlace = $nbrePlace;

        return $this;
    }
}
