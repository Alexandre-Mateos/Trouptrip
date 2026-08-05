<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\PersonalItemResource\PersonalItemInputDTO;
use App\Entity\PersonalItem;
use App\Entity\Trip;
use App\Repository\ParticipationRepository;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PersonalItemVoter extends Voter
{
    public const CREATE = 'PERSONAL_ITEM_CREATE';
    public const EDIT = 'PERSONAL_ITEM_EDIT';

    public function __construct(
        private ParticipationRepository $participationRepository,
        private IriConverterInterface $iriConverter,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT])
            && ($subject instanceof PersonalItem || $subject instanceof PersonalItemInputDTO);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        $trip = null;
        if ($subject instanceof PersonalItem) {
            $trip = $subject->getTrip();
        } elseif ($subject instanceof PersonalItemInputDTO) {
            try {
                /** @var Trip $trip */
                $trip = $this->iriConverter->getResourceFromIri($subject->trip);
            } catch (ItemNotFoundException|InvalidArgumentException) {
                return false;
            }
        }

        if (!$trip) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CREATE:
                if ($user === $trip->getOwner() || $this->participationRepository->isUserAParticipantInTrip($user, $trip)) {
                    return true;
                }
                break;
            case self::EDIT:
                if (($user === $trip->getOwner() || $this->participationRepository->isUserAParticipantInTrip($user, $trip)) && $user === $subject->getOwner()) {
                    return true;
                }
                break;

        }

        return false;
    }
}
