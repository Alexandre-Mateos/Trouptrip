<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Interface\CreatedAtInterface;
use App\Repository\GroupItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations:[
        new GetCollection(
            uriTemplate: '/trips/{tripId}/group_items',
            uriVariables: [
                'tripId' => new Link(
                    fromProperty: 'groupItems',
                    fromClass: Trip::class
                )
            ],
            normalizationContext: ['groups' => 'groupItem:collection']
        ),
        new Post(
            normalizationContext: ['groups' => 'groupItem:collection'],
            denormalizationContext: ['groups' => 'groupItem:create'],
            securityPostDenormalize: "is_granted('GROUP_ITEM_CREATE', object)"
        ),
        new Patch(
            normalizationContext: ['groups' => 'groupItem:collection'],
            denormalizationContext: ['groups' => 'groupItem:create'],
            securityPostDenormalize: "is_granted('GROUP_ITEM_CREATE', object)"
        ),
        new Delete(
            securityPostDenormalize: "is_granted('GROUP_ITEM_CREATE', object)"
        )
    ]
)]
#[ORM\Entity(repositoryClass: GroupItemRepository::class)]
class GroupItem implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupItem:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupItem:collection', 'groupItem:create'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['groupItem:collection', 'groupItem:create'])]
    private ?int $totalQuantity = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupItem:collection', 'groupItem:create'])]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'groupItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupItem:collection', 'groupItem:create'])]
    private ?Trip $trip = null;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'groupItem')]
    private Collection $assignments;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): static
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

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
            $assignment->setGroupItem($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getGroupItem() === $this) {
                $assignment->setGroupItem(null);
            }
        }

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
