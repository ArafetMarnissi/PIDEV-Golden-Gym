<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom de l'abonnement est obligatoire")]
    #[Assert\Regex(pattern: '/^[a-z]+$/i',htmlPattern: '^[a-zA-Z]+$',message:"Le nom de l'abonnement doit contenir que des lettres")]
    private ?string $nomAbonnement = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Il faut donner le prix de l'abonnement")]
    #[Assert\Positive(message:"Prix de l'abonnement doit être positif")]
    private ?float $prixAbonnement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La durée de l'abonnement est obligatoire")]
    private ?string $dureeAbonnement = null;

    #[ORM\OneToMany(mappedBy: 'ReservationAbonnement', targetEntity: Reservation::class)]
    private Collection $reservation;

    public function __construct()
    {
        $this->reservation = new ArrayCollection();
    }

    // #[ORM\OneToOne(mappedBy: 'ReservationAbonnement', cascade: ['persist', 'remove'])]
    // private ?Reservation $reservation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAbonnement(): ?string
    {
        return $this->nomAbonnement;
    }

    public function setNomAbonnement(string $nomAbonnement): self
    {
        $this->nomAbonnement = $nomAbonnement;

        return $this;
    }

    public function getPrixAbonnement(): ?float
    {
        return $this->prixAbonnement;
    }

    public function setPrixAbonnement(float $prixAbonnement): self
    {
        $this->prixAbonnement = $prixAbonnement;

        return $this;
    }

    public function getDureeAbonnement(): ?string
    {
        return $this->dureeAbonnement;
    }

    public function setDureeAbonnement(string $dureeAbonnement): self
    {
        $this->dureeAbonnement = $dureeAbonnement;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
            $reservation->setReservationAbonnement($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getReservationAbonnement() === $this) {
                $reservation->setReservationAbonnement(null);
            }
        }

        return $this;
    }




    // public function getReservation(): ?Reservation
    // {
    //     return $this->reservation;
    // }

    // public function setReservation(?Reservation $reservation): self
    // {
    //     // unset the owning side of the relation if necessary
    //     if ($reservation === null && $this->reservation !== null) {
    //         $this->reservation->setReservationAbonnement(null);
    //     }

    //     // set the owning side of the relation if necessary
    //     if ($reservation !== null && $reservation->getReservationAbonnement() !== $this) {
    //         $reservation->setReservationAbonnement($this);
    //     }

    //     $this->reservation = $reservation;

    //     return $this;
    // }


}
