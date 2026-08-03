<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Enum\ParticipationStatusEnum;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations:[
        new GetCollection(uriTemplate: '/trips/{tripId}/participation',
            uriVariables: [
                'tripId' => new Link(
                    toProperty: 'trip',
                    fromClass: Trip::class
                )
            ],
            normalizationContext:  ['groups' => 'participation:collection'],
        )
    ]
)]
#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:item', 'participation:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: ParticipationStatusEnum::class)]
    #[Groups(['participation:collection'])]
    private ?ParticipationStatusEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participation:collection'])]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participation:collection'])]
    private ?User $participant = null;

    #[ORM\ManyToOne(inversedBy: 'sentParticipations')]
    #[ORM\JoinColumn(nullable: false)]
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
