<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\ParticipationResource\InviteUserDTO;
use App\ApiResource\ParticipationResource\UpdateParticipationStatusDTO;
use App\Enum\ParticipationStatusEnum;
use App\Interface\CreatedAtInterface;
use App\Repository\ParticipationRepository;
use App\State\Processor\PatchParticipationProcessor;
use App\State\Processor\PostParticipationProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/trips/{tripId}/participation',
            uriVariables: [
                'tripId' => new Link(
                    toProperty: 'trip',
                    fromClass: Trip::class
                )
            ],
            normalizationContext: ['groups' => 'participation:collection'],
            security: "is_granted('TRIP_SUB_RESOURCES_READ', request.attributes.get('tripId'))",
        ),
        new GetCollection(
            uriTemplate: '/me/invitations',
            normalizationContext: ['groups' => 'invitation:collection'],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            name: 'get_me_invitations'
        ),
        new Post(
            normalizationContext: ['groups' => 'participation:collection'],
            securityPostDenormalize: "is_granted('PARTICIPATION_CREATE', object)",
            input: InviteUserDTO::class,
            processor: PostParticipationProcessor::class
        ),
        new Patch(
            normalizationContext: ['groups' => 'participation:collection'],
            securityPostDenormalize: "is_granted('PARTICIPATION_EDIT', object)",
            input: UpdateParticipationStatusDTO::class,
            processor: PatchParticipationProcessor::class
        )
    ]
)]
#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation implements CreatedAtInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:item', 'participation:collection', 'invitation:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: ParticipationStatusEnum::class)]
    #[Groups(['participation:collection', 'participation:edit'])]
    private ?ParticipationStatusEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participation:collection', 'invitation:collection'])]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participation:collection'])]
    private ?User $participant = null;

    #[ORM\ManyToOne(inversedBy: 'sentParticipations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invitation:collection'])]
    private ?User $invitedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?ParticipationStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?ParticipationStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(?User $invitedBy): static
    {
        $this->invitedBy = $invitedBy;

        return $this;
    }
}
