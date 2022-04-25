<?php

namespace App\Entity;

use App\Repository\UserRepository;
use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: "email", message: "This email is already used")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_PATIENT = 'ROLE_PATIENT';
    public const ROLE_CLINIC_WORKER = 'ROLE_USER';
    public const ROLE_DOCTOR = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private int $id;

    #[Assert\NotBlank]
    #[ORM\Column(type: "string", length: 50)]
    private string $personalCode;

    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotBlank]
    #[ORM\Column(type: "string", length: 50)]
    private string $name;

    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotBlank]
    #[ORM\Column(type: "string", length: 50)]
    private string $surname;

    #[Assert\Length(min: 2, max: 50)]
    #[Assert\NotBlank]
    #[ORM\Column(type: "string", length: 50)]
    private string $phone;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column(type: "string", length: 50)]
    private string $email;

    #[Assert\DateTime]
    #[ORM\Column(type: "datetime", length: 50, nullable: true)]
    private DateTime $dateOfBirth;

    #[ORM\Column(type: "string", length: 4096)]
    private string $password;

    #[Assert\Length(min: 8, max: 50)]
    #[Assert\NotBlank]
    private ?string $plainPassword;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserService::class, orphanRemoval: true)]
    private $services;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RelatedUser::class, orphanRemoval: true)]
    private $relatedUsers;

    #[ORM\OneToMany(mappedBy: 'relation', targetEntity: Reservation::class, orphanRemoval: true)]
    private $reservations;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->relatedUsers = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $this->roles[] = 'ROLE_PATIENT';

        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPersonalCode(): string
    {
        return $this->personalCode;
    }

    public function setPersonalCode(string $personalCode): void
    {
        $this->personalCode = $personalCode;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getDateOfBirth(): DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTime $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, UserService>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(UserService $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(UserService $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RelatedUser>
     */
    public function getRelatedUsers(): Collection
    {
        return $this->relatedUsers;
    }

    public function addRelatedUser(RelatedUser $relatedUser): self
    {
        if (!$this->relatedUsers->contains($relatedUser)) {
            $this->relatedUsers[] = $relatedUser;
            $relatedUser->setUser($this);
        }

        return $this;
    }

    public function removeRelatedUser(RelatedUser $relatedUser): self
    {
        if ($this->relatedUsers->removeElement($relatedUser)) {
            // set the owning side to null (unless already changed)
            if ($relatedUser->getUser() === $this) {
                $relatedUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setRelation($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getRelation() === $this) {
                $reservation->setRelation(null);
            }
        }

        return $this;
    }
}
