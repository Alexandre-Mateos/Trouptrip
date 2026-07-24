<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\GroupItemUnitEnum;
use App\Interface\CreatedAtInterface;
use App\Repository\GroupItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
            normalizationContext: ['groups' => 'groupItem:collection'],
            security: "is_granted('TRIP_SUB_RESOURCES_READ', request.attributes.get('tripId'))",
        ),
        new Post(
            normalizationContext: ['groups' => 'groupItem:collection'],
            denormalizationContext: ['groups' => 'groupItem:create'],
            securityPostDenormalize: "is_granted('GROUP_ITEM_CREATE', object)"
        ),
        new Patch(
            normalizationContext: ['groups' => 'groupItem:collection'],
            denormalizationContext: ['groups' => 'groupItem:edit'],
            security: "is_granted('GROUP_ITEM_CREATE', object)"
        ),
        new Delete(
            security: "is_granted('GROUP_ITEM_CREATE', object)"
        )
    ]
)]
#[ORM\Entity(repositoryClass: GroupItemRepository::class)]
class GroupItem implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupItem:collection', 'trip:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupItem:collection', 'groupItem:create', 'groupItem:edit'])]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['groupItem:collection', 'groupItem:create', 'groupItem:edit'])]
    #[Assert\NotNull(message: "La quantité totale est requise.")]
    #[Assert\Positive(message: "La quantité doit être supérieure à 0.")]
    private ?int $totalQuantity = null;

    #[ORM\Column(length: 255, enumType: GroupItemUnitEnum::class)]
    #[Groups(['groupItem:collection', 'groupItem:create', 'groupItem:edit'])]
    #[Assert\NotBlank(message: "L'unité est requise (ex: g, kg, pièces...).")]
    private ?GroupItemUnitEnum $unit = null;

    #[ORM\ManyToOne(inversedBy: 'groupItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupItem:create'])]
    #[Assert\NotNull()]
    private ?Trip $trip = null;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'groupItem', cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['groupItem:collection'])]
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

    public function getUnit(): ?GroupItemUnitEnum
    {
        return $this->unit;
    }

    public function setUnit(GroupItemUnitEnum $unit): static
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
