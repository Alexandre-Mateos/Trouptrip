<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\ParticipationResource\InviteUserDTO;
use App\ApiResource\ParticipationResource\UpdateParticipationStatusDTO;
use App\Entity\Participation;
use App\Entity\Trip;
use App\Enum\ParticipationStatusEnum;
use App\Repository\ParticipationRepository;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ParticipationVoter extends Voter
{
    public const CREATE = 'PARTICIPATION_CREATE';
    public const EDIT = 'PARTICIPATION_EDIT';

    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly ParticipationRepository  $participationRepository,
        private readonly RequestStack $requestStack,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT])
            && ($subject instanceof InviteUserDTO || $subject instanceof UpdateParticipationStatusDTO);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');
            return false;
        }

        // Cas 1: Création de la participation = invitation. Uniquement pour l'organisateur
        if ($subject instanceof InviteUserDTO) {
            try {
                $trip = $this->iriConverter->getResourceFromIri($subject->trip);
            } catch (ItemNotFoundException|InvalidArgumentException) {
                return false;
            }

            return $attribute === self::CREATE && isset($trip) && $currentUser === $trip->getOwner();
        }

        // Cas 2: Mise à jour d'une participation parmi les status suivant: LEFT, EXCLUDED
        if ($subject instanceof UpdateParticipationStatusDTO) {
            $participationId = $this->requestStack->getCurrentRequest()?->attributes->get('id');
            if (!$participationId) {
                return false;
            }

            $participation = $this->participationRepository->find($participationId);
            if (!$participation) {
                return false;
            }

            $trip = $participation->getTrip();

            $targetStatus = $subject->status;
            $currentStatus = $participation->getStatus();

            if ($attribute === self::EDIT) {
                // Exclusion d'un participant: uniquement pour l'organisateur. Uniquement si le status est déjà ACCEPTED ou PENDING
                if ($currentUser === $trip->getOwner() && $targetStatus === ParticipationStatusEnum::EXCLUDED->value) {
                    return in_array($currentStatus, [ParticipationStatusEnum::ACCEPTED, ParticipationStatusEnum::PENDING], true);
                }
                // Le participant veut quitter le voyage: Uniquement si il n'est pas le propriétaire, et si il est déjà ACCEPTED ou PENDING
                if ($currentUser === $participation->getParticipant() && $targetStatus === ParticipationStatusEnum::LEFT->value) {
                    return in_array($currentStatus, [ParticipationStatusEnum::ACCEPTED, ParticipationStatusEnum::PENDING], true);
                }

                //Le participant souhaite rejoindre un séjour: Uniquement si il n'est pas le propriétaire, et si il est déjà PENDING
                if ($currentUser === $participation->getParticipant() && $targetStatus === ParticipationStatusEnum::ACCEPTED->value) {
                    return $currentStatus === ParticipationStatusEnum::PENDING;
                }
            }
        }
        // tous les autres cas ne sont refusés par défaut
        return false;
    }
}
