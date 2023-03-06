<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
//#[UniqueEntity('ReservationAbonnement', message: 'Cet abonnement a déjà été réservé.')]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThanOrEqual('today',message:"La date de la reservation doit être supérieur à la date actuelle")]
    private ?\DateTimeInterface $DateFin = null;

    #[ORM\ManyToOne(inversedBy: 'reservation')]
    private ?Abonnement $ReservationAbonnement = null;

    #[ORM\ManyToOne(inversedBy: 'ReservationClient')]
    private ?User $user = null;

    // #[ORM\OneToOne(inversedBy: 'reservation', cascade: ['persist', 'remove'])]
    // private ?Abonnement $ReservationAbonnement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getReservationAbonnement(): ?Abonnement
    {
        return $this->ReservationAbonnement;
    }
    
    public function setReservationAbonnement(?Abonnement $ReservationAbonnement): self
    {
        $this->ReservationAbonnement = $ReservationAbonnement;
    
        return $this;
    }
    public function isAbonnementExpired(): bool
    {
        return $this->getDateFin() && $this->getDateFin() < new \DateTime('@' . strtotime('now'));
    }   

    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
    
        return $this;
    }    

    // public function getReservationAbonnement(): ?Abonnement
    // {
    //     return $this->ReservationAbonnement;
    // }

    // public function setReservationAbonnement(?Abonnement $ReservationAbonnement): self
    // {
    //     $this->ReservationAbonnement = $ReservationAbonnement;

    //     return $this;
    // }

    // #[ORM\PrePersist]
    // #[ORM\PreUpdate]
    // public function expireReservationAbonnement(LifecycleEventArgs $args)
    // {
    //     if ($this->getDateFin() && $this->getDateFin() < new \DateTime('@' . strtotime('now'))) {
    //         $this->setReservationAbonnement(null);
    //     }
    // }

//    #[ORM\PrePersist]
//     #[ORM\PreUpdate]

//     public function prePersist(LifecycleEventArgs $args)
//     {
//         $this->expireReservationAbonnement();
//     }    






}
