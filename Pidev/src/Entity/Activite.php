<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom de l'activité est obligatoire")]
    #[Assert\Length(min:2,max:15,minMessage:"Le nom de l'activité doit comporter au moins {{ limit }} caractéres", maxMessage:"Le nom de l'activité doit comporter au maximum {{ limit }} caractéres")]
    #[Assert\Regex(pattern: '/^[a-z\s]+$/i',htmlPattern: '^[a-zA-Z\s]+$',message:"Le nom de l'activité doit contenir que des lettres")]
    private ?string $nomAcitivite = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"La description de l'activité est obligatoire")]
    #[Assert\Length(min:10,minMessage:"La description de l'activité doit comporter au moins {{ limit }} caractéres")]
    private ?string $descriptionActivite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La durée de l'activité est obligatoire")]
    private ?string $dureeActivite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThanOrEqual('today',message:"La date de l'activité doit être supérieur à la date actuelle")]
    private ?\DateTimeInterface $DateActivite = null;

    /*#[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom du coach est obligatoire")]
    #[Assert\Length(min:2,minMessage:"Le nom du coach doit comporter au moins {{ limit }} caractéres")]
    #[Assert\Regex(pattern: '/^[a-z\s]+$/i',htmlPattern: '^[a-zA-Z\s]+$',message:"Le nom du coach doit contenir que des lettres")]
    private ?string $coach = null;*/

    #[ORM\Column]
    #[Assert\NotBlank(message:"Il faut donner le nombre de places")]
    #[Assert\Positive(message:"Nombre de places doit être positif")]
    private ?int $nbrePlace = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image = null;

    #[ORM\ManyToOne]
    private ?Coach $Coach = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $TimeActivite = null;

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

    /*public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): self
    {
        $this->coach = $coach;

        return $this;
    }*/

    public function getNbrePlace(): ?int
    {
        return $this->nbrePlace;
    }

    public function setNbrePlace(int $nbrePlace): self
    {
        $this->nbrePlace = $nbrePlace;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->Coach;
    }

    public function setCoach(?Coach $Coach): self
    {
        $this->Coach = $Coach;

        return $this;
    }

    public function getTimeActivite(): ?\DateTimeInterface
    {
        return $this->TimeActivite;
    }

    public function setTimeActivite(?\DateTimeInterface $TimeActivite): self
    {
        $this->TimeActivite = $TimeActivite;

        return $this;
    }
}
