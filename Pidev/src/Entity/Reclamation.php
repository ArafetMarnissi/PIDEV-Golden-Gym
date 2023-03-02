<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"La date de la réclamation est obligatoire")]
   
    #[Assert\Date(message:"La date de la réclamation doit etre de type date")]
    private ?String $date_Reclamtion = null;

    #[ORM\Column(type: Types::TEXT)]    
    #[Assert\NotBlank(message:"La description de la réclamation est obligatoire")]
    #[Assert\Length(min:10,minMessage:"La description de la réclamation doit comporter au moins {{ limit }} caractéres")]
    private ?string $descriptionReclamation= null;


   
    

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"Le type de la réclamation est obligatoire")]
    private ?String $type_reclamation = null;

    #[ORM\OneToOne(inversedBy: 'idreclamation', cascade: ['persist', 'remove'])]
    private ?Reponse $idreponse = null;
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReclamtion(): ?String
    {
        return $this->date_Reclamtion;
    }

    public function setDateReclamtion(String $date_Reclamtion): self
    {
        $this->date_Reclamtion = $date_Reclamtion;

        return $this;
    }

    public function getDescriptionReclamation(): ?string
    {
        return $this->descriptionReclamation;
    }

    public function setDescriptionReclamation(string $descriptionReclamation): self
    {
        $this->descriptionReclamation = $descriptionReclamation;

        return $this;
    }

  

    public function getTypeReclamation(): ?string
    {
        return $this->type_reclamation;
    }

    public function setTypeReclamation(string $type_reclamation): self
    {
        $this->type_reclamation = $type_reclamation;

        return $this;
    }
    public function _toString(){
        return (string) $this->type_reclamation;
    }

    public function getIdreponse(): ?Reponse
    {
        return $this->idreponse;
    }

    public function setIdreponse(?Reponse $idreponse): self
    {
        $this->idreponse = $idreponse;

        return $this;
    }
   
   
}
