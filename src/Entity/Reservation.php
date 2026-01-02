<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?\DateTime $date_rdv = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $total_price = null;

    #[ORM\Column]
    private ?int $total_duration = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $guest_firstname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $guest_lastname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $guest_email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $guest_phone = null;

    #[ORM\Column(length: 255)]
    private ?string $visit_address = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $client = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'reservations')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDateRdv(): ?\DateTime
    {
        return $this->date_rdv;
    }

    public function setDateRdv(\DateTime $date_rdv): static
    {
        $this->date_rdv = $date_rdv;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getTotalDuration(): ?int
    {
        return $this->total_duration;
    }

    public function setTotalDuration(int $total_duration): static
    {
        $this->total_duration = $total_duration;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getGuestFirstname(): ?string
    {
        return $this->guest_firstname;
    }

    public function setGuestFirstname(?string $guest_firstname): static
    {
        $this->guest_firstname = $guest_firstname;

        return $this;
    }

    public function getGuestLastname(): ?string
    {
        return $this->guest_lastname;
    }

    public function setGuestLastname(?string $guest_lastname): static
    {
        $this->guest_lastname = $guest_lastname;

        return $this;
    }

    public function getGuestEmail(): ?string
    {
        return $this->guest_email;
    }

    public function setGuestEmail(?string $guest_email): static
    {
        $this->guest_email = $guest_email;

        return $this;
    }

    public function getGuestPhone(): ?string
    {
        return $this->guest_phone;
    }

    public function setGuestPhone(?string $guest_phone): static
    {
        $this->guest_phone = $guest_phone;

        return $this;
    }

    public function getVisitAddress(): ?string
    {
        return $this->visit_address;
    }

    public function setVisitAddress(string $visit_address): static
    {
        $this->visit_address = $visit_address;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        $this->services->removeElement($service);

        return $this;
    }
}
