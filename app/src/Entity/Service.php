<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $title;

    #[ORM\Column(type: 'string', length: 2000, nullable: true)]
    private $description;

    #[ORM\Column(type: 'float', nullable: true)]
    private $price;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $is_active;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: UserService::class, orphanRemoval: true)]
    private $users;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: ServiceImage::class, orphanRemoval: true)]
    private $serviceImages;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->serviceImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection<int, UserService>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserService $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setService($this);
        }

        return $this;
    }

    public function removeUser(UserService $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getService() === $this) {
                $user->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceImage>
     */
    public function getServiceImages(): Collection
    {
        return $this->serviceImages;
    }

    public function addServiceImage(ServiceImage $serviceImage): self
    {
        if (!$this->serviceImages->contains($serviceImage)) {
            $this->serviceImages[] = $serviceImage;
            $serviceImage->setService($this);
        }

        return $this;
    }

    public function removeServiceImage(ServiceImage $serviceImage): self
    {
        if ($this->serviceImages->removeElement($serviceImage)) {
            // set the owning side to null (unless already changed)
            if ($serviceImage->getService() === $this) {
                $serviceImage->setService(null);
            }
        }

        return $this;
    }
}
