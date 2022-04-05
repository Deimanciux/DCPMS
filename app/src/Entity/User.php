<?php

namespace App\Entity;

use App\Repository\UserRepository;
use \DateTime;
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
    const ROLE_PATIENT = 'ROLE_PATIENT';
    const ROLE_CLINIC_WORKER = 'ROLE_USER';
    const ROLE_DOCTOR = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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
}
