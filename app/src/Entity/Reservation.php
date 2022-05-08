<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Service $service = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $endDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'patientReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $doctor = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private string $reasonOfVisit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getReasonOfVisit(): ?string
    {
        return $this->reasonOfVisit;
    }

    public function setReasonOfVisit(?string $reasonOfVisit): self
    {
        $this->reasonOfVisit = $reasonOfVisit;

        return $this;
    }
}
