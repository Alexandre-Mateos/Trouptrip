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
    public const EXCLUDE = 'PARTICIPATION_EXCLUDE';

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
        return in_array($attribute, [self::CREATE, self::EXCLUDE])
            && ($subject instanceof InviteUserDTO || $subject instanceof UpdateParticipationStatusDTO);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        if ($subject instanceof InviteUserDTO) {
            try {
                $trip = $this->iriConverter->getResourceFromIri($subject->trip);
            } catch (ItemNotFoundException|InvalidArgumentException) {
                return false;
            }
        }

        if ($subject instanceof UpdateParticipationStatusDTO) {
            $id = $this->requestStack->getCurrentRequest()?->attributes->get('id');

            if (!$id) {
                return false;
            }

            $participation = $this->participationRepository->find($id);

            if (!$participation) {
                return false;
            }

            $trip = $participation->getTrip();
        }

        switch ($attribute) {
            case self::CREATE:
                if (isset($trip) && $user === $trip->getOwner()) {
                    return true;
                }
                break;
            case self::EXCLUDE:
                if (isset($trip, $participation) && $user === $trip->getOwner() && in_array($participation->getStatus(), [ParticipationStatusEnum::ACCEPTED, ParticipationStatusEnum::PENDING], true)) {
                    return true;
                }
        }

        return false;
    }
}
