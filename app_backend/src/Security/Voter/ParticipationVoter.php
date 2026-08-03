<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\ParticipationResource\InviteUserDTO;
use App\Entity\Participation;
use App\Entity\Trip;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ParticipationVoter extends Voter
{
    public const CREATE = 'PARTICIPATION_CREATE';

    public function __construct(
        private readonly IriConverterInterface $iriConverter
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE])
            && ($subject instanceof Participation || $subject instanceof InviteUserDTO);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        if($subject instanceof Participation){
            $trip = $subject->getTrip();
        }else{
            $trip = $this->iriConverter->getResourceFromIri($subject->trip);
        }

        if(!$trip instanceof Trip){
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                if ($user === $trip->getOwner()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
