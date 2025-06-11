<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cart')]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Medicament::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medicament $medicament = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    #[ORM\Column(type: 'boolean')]
    private bool $requiresApproval = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMedicament(): ?Medicament
    {
        return $this->medicament;
    }

    public function setMedicament(?Medicament $medicament): self
    {
        $this->medicament = $medicament;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getRequiresApproval(): bool
    {
        return $this->requiresApproval;
    }

    public function setRequiresApproval(bool $requiresApproval): self
    {
        $this->requiresApproval = $requiresApproval;
        return $this;
    }
}