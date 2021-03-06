<?php

namespace App\Entity;

use App\Repository\UserRepository;
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
    public const ROLE_CLINIC_WORKER = 'ROLE_CLINIC_WORKER';
    public const ROLE_DOCTOR = 'ROLE_DOCTOR';
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

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $avatar;

    #[Assert\DateTime]
    #[ORM\Column(type: "datetime_immutable", length: 50, nullable: true)]
    private ?\DateTimeImmutable $dateOfBirth;

    #[ORM\Column(type: "string", length: 4096)]
    private string $password;

    #[Assert\Length(min: 8, max: 50)]
    private ?string $plainPassword;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserService::class, orphanRemoval: true)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RelatedUser::class, orphanRemoval: true)]
    private Collection $relatedUsers;

    #[ORM\OneToMany(mappedBy: 'relation', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Reservation::class)]
    private Collection $patientReservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: HealthRecord::class, orphanRemoval: true)]
    private Collection $healthRecords;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: HealthRecord::class, orphanRemoval: true)]
    private Collection $doctorHealthRecords;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WorkSchedule::class)]
    private $workSchedules;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tooth::class, orphanRemoval: true)]
    private $teeth;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->relatedUsers = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->patientReservations = new ArrayCollection();
        $this->healthRecords = new ArrayCollection();
        $this->doctorHealthRecords = new ArrayCollection();
        $this->workSchedules = new ArrayCollection();
        $this->teeth = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullName();
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
        if ($this->roles === []) {
            $this->roles[] = self::ROLE_PATIENT;
        }

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

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeImmutable $dateOfBirth): void
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

    /**
     * @return ?string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return ?string
     */
    public function getAvatarUrl(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (str_contains($this->avatar, '/')) {
            return $this->avatar;
        }

        return sprintf('/images/users/%s', $this->avatar);
    }

    /**
     * @param ?string $avatar
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getPatientReservations(): Collection
    {
        return $this->patientReservations;
    }

    public function addPatientReservation(Reservation $patientReservation): self
    {
        if (!$this->patientReservations->contains($patientReservation)) {
            $this->patientReservations[] = $patientReservation;
            $patientReservation->setDoctor($this);
        }

        return $this;
    }

    public function removePatientReservation(Reservation $patientReservation): self
    {
        if ($this->patientReservations->removeElement($patientReservation)) {
            // set the owning side to null (unless already changed)
            if ($patientReservation->getDoctor() === $this) {
                $patientReservation->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HealthRecord>
     */
    public function getHealthRecords(): Collection
    {
        return $this->healthRecords;
    }

    public function addHealthRecord(HealthRecord $healthRecord): self
    {
        if (!$this->healthRecords->contains($healthRecord)) {
            $this->healthRecords[] = $healthRecord;
            $healthRecord->setUser($this);
        }

        return $this;
    }

    public function removeHealthRecord(HealthRecord $healthRecord): self
    {
        if ($this->healthRecords->removeElement($healthRecord)) {
            // set the owning side to null (unless already changed)
            if ($healthRecord->getUser() === $this) {
                $healthRecord->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HealthRecord>
     */
    public function getDoctorHealthRecords(): Collection
    {
        return $this->doctorHealthRecords;
    }

    public function addDoctorHealthRecord(HealthRecord $healthRecord): self
    {
        if (!$this->doctorHealthRecords->contains($healthRecord)) {
            $this->doctorHealthRecords[] = $healthRecord;
            $healthRecord->setUser($this);
        }

        return $this;
    }

    public function removeDoctorHealthRecord(HealthRecord $healthRecord): self
    {
        if ($this->doctorHealthRecords->removeElement($healthRecord)) {
            // set the owning side to null (unless already changed)
            if ($healthRecord->getUser() === $this) {
                $healthRecord->setUser(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf("%s %s", $this->name, $this->surname);
    }

    /**
     * @return Collection<int, WorkSchedule>
     */
    public function getWorkSchedules(): Collection
    {
        return $this->workSchedules;
    }

    public function addWorkSchedule(WorkSchedule $workSchedule): self
    {
        if (!$this->workSchedules->contains($workSchedule)) {
            $this->workSchedules[] = $workSchedule;
            $workSchedule->setUser($this);
        }

        return $this;
    }

    public function removeWorkSchedule(WorkSchedule $workSchedule): self
    {
        if ($this->workSchedules->removeElement($workSchedule)) {
            // set the owning side to null (unless already changed)
            if ($workSchedule->getUser() === $this) {
                $workSchedule->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tooth>
     */
    public function getTeeth(): Collection
    {
        return $this->teeth;
    }

    public function addTooth(Tooth $tooth): self
    {
        if (!$this->teeth->contains($tooth)) {
            $this->teeth[] = $tooth;
            $tooth->setUser($this);
        }

        return $this;
    }

    public function removeTooth(Tooth $tooth): self
    {
        if ($this->teeth->removeElement($tooth)) {
            // set the owning side to null (unless already changed)
            if ($tooth->getUser() === $this) {
                $tooth->setUser(null);
            }
        }

        return $this;
    }

    public function isPatient(): bool
    {
        return in_array(self::ROLE_PATIENT, $this->roles, true);
    }
}
