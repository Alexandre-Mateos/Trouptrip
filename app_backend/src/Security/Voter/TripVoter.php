<?php

namespace App\Security\Voter;

use App\Entity\Trip;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TripVoter extends Voter
{
    public const EDIT = 'TRIP_EDIT';
    public const READ = 'TRIP_READ';
    public const  DELETE = 'TRIP_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::READ, self::DELETE])
            && $subject instanceof Trip;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
            case self::EDIT:
                return $user === $subject->getOwner();

            case self::READ:
                return $user === $subject->getOwner()
                    || $subject->hasAcceptedParticipant($user);
        }

        return false;
    }
}
