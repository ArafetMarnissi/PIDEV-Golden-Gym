<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReponse = null;

  
    // #[ORM\Column(type: Types::TEXT)]
    // #[Assert\NotBlank(message:"La description de la réponse est obligatoire")]
    // #[Assert\Length(min:10,minMessage:"La description de la réponse doit comporter au moins {{ limit }} caractéres")]
    // private ?string $descriptionreponse = null;

    // #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    // #[ORM\JoinColumn(onDelete: false)]
    // private ?Reclamation $idreclamation = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message:"La description de la réponse est obligatoire")]
    #[Assert\Length(min:10,minMessage:"La description de la réponse doit comporter au moins {{ limit }} caractéres")]
    private ?string $descriptionreponse = null;

    #[ORM\OneToOne(mappedBy: 'idreponse', cascade: ['persist', 'remove'])]
    private ?Reclamation $idreclamation = null;

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


    

    // public function getIdreclamation(): ?Reclamation
    // {
    //     return $this->idreclamation;
    // }

    // public function setIdreclamation(Reclamation $idreclamation): self
    // {
    //     $this->idreclamation = $idreclamation;

    //     return $this;
    // }

    public function getDescriptionreponse(): ?string
    {
        return $this->descriptionreponse;
    }

    public function setDescriptionreponse(?string $descriptionreponse): self
    {
        $this->descriptionreponse = $descriptionreponse;

        return $this;
    }

    public function getIdreclamation(): ?Reclamation
    {
        return $this->idreclamation;
    }

    public function setIdreclamation(?Reclamation $idreclamation): self
    {
        // unset the owning side of the relation if necessary
        if ($idreclamation === null && $this->idreclamation !== null) {
            $this->idreclamation->setIdreponse(null);
        }

        // set the owning side of the relation if necessary
        if ($idreclamation !== null && $idreclamation->getIdreponse() !== $this) {
            $idreclamation->setIdreponse($this);
        }

        $this->idreclamation = $idreclamation;

        return $this;
    }
   
}
