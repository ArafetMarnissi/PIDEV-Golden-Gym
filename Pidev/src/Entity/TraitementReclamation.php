<?php

namespace App\Entity;

use App\Repository\TraitementReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraitementReclamationRepository::class)]
class TraitementReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateReponse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $DescriptionReponse = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Reclamation $reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(\DateTimeInterface $dateReponse): self
    {
        $this->dateReponse = $dateReponse;

        return $this;
    }

    public function getDescriptionReponse(): ?string
    {
        return $this->DescriptionReponse;
    }

    public function setDescriptionReponse(string $DescriptionReponse): self
    {
        $this->DescriptionReponse = $DescriptionReponse;

        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
