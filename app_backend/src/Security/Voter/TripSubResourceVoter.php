<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\TripRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TripSubResourceVoter extends Voter
{
    public const READ = 'TRIP_SUB_RESOURCES_READ';

    public function __construct(
        private TripRepository $tripRepository
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::READ && (is_numeric($subject) || is_string($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        $trip = $this->tripRepository->findOneBy(['id' =>  $subject]);
        if (!$trip) {
            return false;
        }


        switch ($attribute) {
            case self::READ:
                if($user === $trip->getOwner() || $trip->hasAcceptedParticipant($user)){
                    return true;
                }
                break;
        }

        return false;
    }
}
