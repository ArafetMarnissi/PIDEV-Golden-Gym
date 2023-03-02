<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
class Coach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom du coach est obligatoire")]
    #[Assert\Length(min:2,max:15,minMessage:"Le nom du coach doit comporter au moins {{ limit }} caractéres", maxMessage:"Le nom du coach doit comporter au maximum {{ limit }} caractéres")]
    #[Assert\Regex(pattern: '/^[a-z\s]+$/i',htmlPattern: '^[a-zA-Z\s]+$',message:"Le nom du coach doit contenir que des lettres")]
    private ?string $nomCoach = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Il faut donner l'age du coach")]
    #[Assert\Positive(message:"L'age du coach doit être positif")]
    #[Assert\GreaterThanOrEqual(value:18,message:"L'age du coach doit être supérieur à 18 ")]
    private ?int $ageCoach = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCoach(): ?string
    {
        return $this->nomCoach;
    }

    public function setNomCoach(string $nomCoach): self
    {
        $this->nomCoach = $nomCoach;

        return $this;
    }

    public function getAgeCoach(): ?int
    {
        return $this->ageCoach;
    }

    public function setAgeCoach(int $ageCoach): self
    {
        $this->ageCoach = $ageCoach;

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
}
