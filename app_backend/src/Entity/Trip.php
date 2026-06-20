<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\ParticipationStatusEnum;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'trip:collection']
        ),
        new Get(
            normalizationContext: ['groups' => 'trip:item'],
            security: "is_granted('TRIP_READ', object)"
        )
    ]
)]
#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trip:collection', 'trip:item'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['trip:item'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column]
    #[Groups(['trip:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'trip')]
    #[Groups(['trip:item'])]
    private Collection $participations;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'trip')]
    private Collection $activities;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'trip')]
    private Collection $tasks;

    /**
     * @var Collection<int, GroupItem>
     */
    #[ORM\OneToMany(targetEntity: GroupItem::class, mappedBy: 'trip')]
    private Collection $groupItems;

    /**
     * @var Collection<int, Refund>
     */
    #[ORM\OneToMany(targetEntity: Refund::class, mappedBy: 'trip')]
    private Collection $refunds;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'tri�p')]
    private Collection $expenses;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->groupItems = new ArrayCollection();
        $this->refunds = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setTrip($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getTrip() === $this) {
                $participation->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setTrip($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getTrip() === $this) {
                $activity->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setTrip($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTrip() === $this) {
                $task->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupItem>
     */
    public function getGroupItems(): Collection
    {
        return $this->groupItems;
    }

    public function addGroupItem(GroupItem $groupItem): static
    {
        if (!$this->groupItems->contains($groupItem)) {
            $this->groupItems->add($groupItem);
            $groupItem->setTrip($this);
        }

        return $this;
    }

    public function removeGroupItem(GroupItem $groupItem): static
    {
        if ($this->groupItems->removeElement($groupItem)) {
            // set the owning side to null (unless already changed)
            if ($groupItem->getTrip() === $this) {
                $groupItem->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Refund>
     */
    public function getRefunds(): Collection
    {
        return $this->refunds;
    }

    public function addRefund(Refund $refund): static
    {
        if (!$this->refunds->contains($refund)) {
            $this->refunds->add($refund);
            $refund->setTrip($this);
        }

        return $this;
    }

    public function removeRefund(Refund $refund): static
    {
        if ($this->refunds->removeElement($refund)) {
            // set the owning side to null (unless already changed)
            if ($refund->getTrip() === $this) {
                $refund->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setTrip($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getTrip() === $this) {
                $expense->setTrip(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function hasAcceptedParticipant(User $user): bool
    {
        foreach ($this->participations as $participation) {
            if ($user === $participation->getParticipant() && ParticipationStatusEnum::ACCEPTED === $participation->getStatus()) {
                return true;
            }
        }
        return false;
    }
}
