<?php

namespace App\Entity;

use App\Repository\DebtorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebtorRepository::class)]
class Debtor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $debtor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Expense $expense = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebtor(): ?User
    {
        return $this->debtor;
    }

    public function setDebtor(?User $debtor): static
    {
        $this->debtor = $debtor;

        return $this;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(?Expense $expense): static
    {
        $this->expense = $expense;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
