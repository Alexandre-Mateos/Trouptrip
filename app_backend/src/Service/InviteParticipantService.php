<?php

namespace App\Service;

use App\Entity\Participation;
use App\Entity\Trip;
use App\Entity\User;
use App\Enum\ParticipationStatusEnum;
use App\Repository\UserRepository;

readonly class InviteParticipantService
{
    public function __construct(
        private UserRepository $userRepository
    )
    {

    }

    public function inviteParticipant(string $participantEmail, User $user, Trip $trip): ?Participation
    {
        $participant = $this->userRepository->findOneBy(['email' => $participantEmail]);
        // Pour le moment si l'utilisateur n'existe pas déjà dans Trouptrip, il est ignoré
        // A implémenter: envois d'email pour inviter le user en question à créer un compte

        if (null === $participant) {
            return null;
        }

        $participation = new Participation();
        $participation->setStatus(ParticipationStatusEnum::PENDING)
            ->setParticipant($participant)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setInvitedBy($user)
            ->setTrip($trip);

        return $participation;
    }
}
