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
use App\Repository\PersonalItemRepository;
use App\State\Processor\PersonalItemProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/trips/{tripId}/personal_items',
            uriVariables: [
                'tripId' => new Link(
                    toProperty: 'trip',
                    fromClass: Trip::class
                )
            ],
            normalizationContext: ['groups' => ['personal_item:collection']],
            security: "is_granted('TRIP_SUB_RESOURCES_READ', request.attributes.get('tripId'))",
        ),
        new Post(
            normalizationContext: ['groups' => 'personal_item:collection'],
            denormalizationContext: ['groups' => 'personal_item:create'],
            securityPostDenormalize: "is_granted('PERSONAL_ITEM_CREATE', object)",
            processor: PersonalItemProcessor::class
        ),
        new Patch(
            normalizationContext: ['groups' => 'personal_item:collection'],
            denormalizationContext: ['groups' => 'personal_item:update'],
            security:  "is_granted('PERSONAL_ITEM_EDIT', object) ",
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getOwner() === user"
        )
    ]
)]
#[ORM\Entity(repositoryClass: PersonalItemRepository::class)]
class PersonalItem implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['personal_item:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['personal_item:collection', 'personal_item:create', 'personal_item:update'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['personal_item:collection', 'personal_item:create', 'personal_item:update'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['personal_item:collection'])]
    private ?bool $isPacked = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['personal_item:collection', 'personal_item:create'])]
    private ?Trip $trip = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, enumType: GroupItemUnitEnum::class)]
    #[Groups(['personal_item:collection', 'personal_item:create', 'personal_item:update'])]
    private ?GroupItemUnitEnum $unit = null;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isPacked(): ?bool
    {
        return $this->isPacked;
    }

    public function setIsPacked(bool $isPacked): static
    {
        $this->isPacked = $isPacked;

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

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

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

    public function getUnit(): ?GroupItemUnitEnum
    {
        return $this->unit;
    }

    public function setUnit(GroupItemUnitEnum $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
