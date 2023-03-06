<?php

namespace App\Entity;

use DateTime;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("commandes")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("commandes")]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'votre adresse de livraison n\'est pas valide',

    )]
    #[Groups("commandes")]
    private ?string $AdresseLivraison = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups("commandes")]
    private ?float $prixCommande = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups("commandes")]
    private ?string $methodePaiement = null;


    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, orphanRemoval: true)]
    private Collection $ligneCommandes;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/^\d+$/", message: "L'attribut ne peut contenir que des chiffres.")]
    #[Assert\Length(
        min: 8,
        max: 8,
        minMessage: 'votre numéro de téléphone n\'est pas valide',
    )]
    #[Groups("commandes")]
    private ?string $telephone = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
   
    private ?User $User = null;
    //////////
    #[Groups("commandes")]
    private ?int $UserId = null;
        /////
    public function __construct()
    {
        $this->dateCommande = new DateTime();
        $this->ligneCommandes = new ArrayCollection();
    }
    ////
    public function getUserId(): ?int
    {
        return $this->UserId;
    }
    public function setUserId(?int $UserId): ?int
    {
        return $this->UserId=$UserId;
    }
    ////
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->AdresseLivraison;
    }

    public function setAdresseLivraison(?string $AdresseLivraison): self
    {
        $this->AdresseLivraison = $AdresseLivraison;

        return $this;
    }

    public function getPrixCommande(): ?float
    {
        return $this->prixCommande;
    }

    public function setPrixCommande(float $prixCommande): self
    {
        $this->prixCommande = $prixCommande;

        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(string $methodePaiement): self
    {
        $this->methodePaiement = $methodePaiement;

        return $this;
    }



    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
