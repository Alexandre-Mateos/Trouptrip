<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Interface\CreatedAtInterface;
use App\Repository\AssignmentRepository;
use App\State\Processor\PostAssignmentProcessor;
use App\State\Provider\AssignmentProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/trips/{tripId}/assignments',
            uriVariables: [
                'tripId' => new Link(
                    fromClass: Trip::class
                )
            ],
            normalizationContext: ['groups' => 'assignment:collection'],
            security: "is_granted('TRIP_SUB_RESOURCES_READ', request.attributes.get('tripId'))",
            provider: AssignmentProvider::class,
        ),
        new Post(
            normalizationContext: ['groups' => 'assignment:collection'],
            denormalizationContext: ['groups' => 'assignment:create'],
            securityPostDenormalize: "is_granted('ASSIGNMENT_CREATE', object)",
            processor: PostAssignmentProcessor::class
        ),
        new Patch(
            normalizationContext: ['groups' => 'assignment:collection'],
            denormalizationContext: ['groups' => 'assignment:edit'],
            security: "is_granted('ASSIGNMENT_CREATE', object)"
        ),
        new Delete(
            security: "is_granted('ASSIGNMENT_CREATE', object)"
        )
    ]
)]
#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['assignment:collection', 'groupItem:collection'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['assignment:create', 'assignment:collection', 'assignment:edit'])]
    private ?int $assignedQuantity = null;

    #[ORM\Column]
    #[Groups(['assignment:collection'])]
    private ?bool $IsPacked = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assignment:create'])]
    private ?GroupItem $groupItem = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assignment:collection'])]
    private ?User $assignedTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignedQuantity(): ?int
    {
        return $this->assignedQuantity;
    }

    public function setAssignedQuantity(int $assignedQuantity): static
    {
        $this->assignedQuantity = $assignedQuantity;

        return $this;
    }

    public function isPacked(): ?bool
    {
        return $this->IsPacked;
    }

    public function setIsPacked(bool $IsPacked): static
    {
        $this->IsPacked = $IsPacked;

        return $this;
    }

    public function getGroupItem(): ?GroupItem
    {
        return $this->groupItem;
    }

    public function setGroupItem(?GroupItem $groupItem): static
    {
        $this->groupItem = $groupItem;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

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
