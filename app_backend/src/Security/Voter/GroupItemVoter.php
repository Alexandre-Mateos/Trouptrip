<?php

namespace App\Security\Voter;

use App\Entity\GroupItem;
use App\Repository\ParticipationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class GroupItemVoter extends Voter
{
    public const CREATE = 'GROUP_ITEM_CREATE';

    public function __construct(
        private ParticipationRepository $participationRepository,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE])
            && $subject instanceof GroupItem;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');
            return false;
        }

        $trip = $subject->getTrip();
        if (!$trip) {
            $vote?->addReason('The ressource must be associated to a trip');
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                if ($user === $trip->getOwner() || $this->participationRepository->isUserAParticipantInTrip($user, $trip)) {
                    return true;
                }
                break;

        }

        return false;
    }
}
