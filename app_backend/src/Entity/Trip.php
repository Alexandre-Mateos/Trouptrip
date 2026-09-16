<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ComparisonFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Enum\ParticipationStatusEnum;
use App\Interface\CreatedAtInterface;
use App\Repository\TripRepository;
use App\State\Processor\DeleteTripProcessor;
use App\State\Processor\PostTripProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as TripAssert;

#[ApiResource(
    operations: [
        new GetCollection(
            paginationItemsPerPage: 5,
            order: ['startDate' => 'ASC'],
            normalizationContext: ['groups' => 'trip:collection'],
            security: "is_granted('ROLE_USER')",
            parameters: [
                'endDate' => new QueryParameter(
                    filter: new ComparisonFilter(new ExactFilter()),
                    property: 'endDate',
                ),
            ]
        ),
        new GetCollection(
            uriTemplate: '/trips/calendar',
            paginationEnabled: false,
            normalizationContext: ['groups' => 'trip:collection'],
            security: "is_granted('ROLE_USER')",
            parameters: [
                'startDate' => new QueryParameter(
                    filter: new ComparisonFilter(new ExactFilter()),
                    property: 'startDate',
                    required: true,
                ),
                'endDate' => new QueryParameter(
                    filter: new ComparisonFilter(new ExactFilter()),
                    property: 'endDate',
                    required: true,
                ),
            ]
        ),
        new Get(
            normalizationContext: ['groups' => 'trip:item'],
            security: "is_granted('TRIP_READ', object)"
        ),
        new Post(
            normalizationContext: ['groups' => 'trip:item'],
            denormalizationContext: ['groups' => 'trip:create'],
            security: "is_granted('ROLE_USER')",
            processor: PostTripProcessor::class
        ),
        new Patch(
            normalizationContext: ['groups' => 'trip:item'],
            denormalizationContext: ['groups' => 'trip:create'],
            security: "is_granted('TRIP_EDIT', object)"
        ),
        new Delete(
            security: "is_granted('TRIP_DELETE', object)",
            processor: DeleteTripProcessor::class
        )
    ]
)]

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[TripAssert\RangeDate]
class Trip implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item', 'personal_item:collection', 'participation:collection', 'invitation:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trip:collection', 'trip:item', 'trip:create', 'invitation:collection'])]
    #[Assert\NotBlank(message: 'Merci d\'indiquer un titre')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['trip:item', 'trip:create'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item', 'trip:create', 'invitation:collection'])]
    #[Assert\NotBlank(message: 'Merci d\'indiquer une date de début')]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
    #[Groups(['trip:collection', 'trip:item', 'trip:create', 'invitation:collection'])]
    #[Assert\NotBlank(message: 'Merci d\'indiquer une date de début')]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column]
    #[Groups(['trip:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trip:item'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, GroupItem>
     */
    #[ORM\OneToMany(targetEntity: GroupItem::class, mappedBy: 'trip')]
    #[Groups(['trip:item'])]
    private Collection $groupItems;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $isDeleted = false;

    /**
     * @var Collection<int, TripMember>
     */
    #[ORM\OneToMany(targetEntity: TripMember::class, mappedBy: 'trip')]
    private Collection $tripMembers;

    /**
     * @var Collection<int, Invitation>
     */
    #[ORM\OneToMany(targetEntity: Invitation::class, mappedBy: 'trip')]
    private Collection $invitations;

    public function __construct()
    {
        $this->groupItems = new ArrayCollection();
        $this->tripMembers = new ArrayCollection();
        $this->invitations = new ArrayCollection();
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Collection<int, TripMember>
     */
    public function getTripMembers(): Collection
    {
        return $this->tripMembers;
    }

    public function addTripMember(TripMember $tripMember): static
    {
        if (!$this->tripMembers->contains($tripMember)) {
            $this->tripMembers->add($tripMember);
            $tripMember->setTrip($this);
        }

        return $this;
    }

    public function removeTripMember(TripMember $tripMember): static
    {
        if ($this->tripMembers->removeElement($tripMember)) {
            // set the owning side to null (unless already changed)
            if ($tripMember->getTrip() === $this) {
                $tripMember->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setTrip($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getTrip() === $this) {
                $invitation->setTrip(null);
            }
        }

        return $this;
    }
}
