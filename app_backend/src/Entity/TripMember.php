<?php

namespace App\Entity;

use App\Enum\TripMemberRoleEnum;
use App\Enum\TripMemberStatusEnum;
use App\Repository\TripMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripMemberRepository::class)]
class TripMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: TripMemberRoleEnum::class)]
    private ?TripMemberRoleEnum $role = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\Column(length: 50, enumType: TripMemberStatusEnum::class)]
    private ?TripMemberStatusEnum $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tripMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $member = null;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'tripMember')]
    private Collection $assignments;

    /**
     * @var Collection<int, PersonalItem>
     */
    #[ORM\OneToMany(targetEntity: PersonalItem::class, mappedBy: 'owner')]
    private Collection $personalItems;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\OneToMany(targetEntity: Invitation::class, mappedBy: 'invitedBy')]
    private Collection $sentInvitations;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
        $this->personalItems = new ArrayCollection();
        $this->sentInvitations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?TripMemberRoleEnum
    {
        return $this->role;
    }

    public function setRole(TripMemberRoleEnum $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    public function getStatus(): ?TripMemberStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?TripMemberStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Collection<int, Assignment>
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): static
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments->add($assignment);
            $assignment->setTripMember($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getTripMember() === $this) {
                $assignment->setTripMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonalItem>
     */
    public function getPersonalItems(): Collection
    {
        return $this->personalItems;
    }

    public function addPersonalItem(PersonalItem $personalItem): static
    {
        if (!$this->personalItems->contains($personalItem)) {
            $this->personalItems->add($personalItem);
            $personalItem->setOwner($this);
        }

        return $this;
    }

    public function removePersonalItem(PersonalItem $personalItem): static
    {
        if ($this->personalItems->removeElement($personalItem)) {
            // set the owning side to null (unless already changed)
            if ($personalItem->getOwner() === $this) {
                $personalItem->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSentInvitations(): Collection
    {
        return $this->sentInvitations;
    }

    public function addSentInvitation(Invitation $sentInvitation): static
    {
        if (!$this->sentInvitations->contains($sentInvitation)) {
            $this->sentInvitations->add($sentInvitation);
            $sentInvitation->setInvitedBy($this);
        }

        return $this;
    }

    public function removeSentInvitation(Invitation $sentInvitation): static
    {
        if ($this->sentInvitations->removeElement($sentInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sentInvitation->getInvitedBy() === $this) {
                $sentInvitation->setInvitedBy(null);
            }
        }

        return $this;
    }
}
