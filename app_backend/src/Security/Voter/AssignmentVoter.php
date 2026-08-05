<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\AssignmentResource\AssignmentInputDTO;
use App\Entity\Assignment;
use App\Repository\AssignmentRepository;
use App\Repository\ParticipationRepository;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssignmentVoter extends Voter
{
    public const CREATE = 'ASSIGNMENT_CREATE';
    public const DELETE = 'ASSIGNMENT_DELETE';

    public function __construct(
        private readonly ParticipationRepository $participationRepository,
        private readonly RequestStack $requestStack,
        private readonly AssignmentRepository $assignmentRepository,
        private readonly IriConverterInterface $iriConverter
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::DELETE])
            && ($subject instanceof Assignment || $subject instanceof AssignmentInputDTO);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        $assignmentId = $request?->attributes->get('id');

        if ($assignmentId) {
            $assignment = $this->assignmentRepository->find($assignmentId);
            $groupItem = $assignment?->getGroupItem();
        } else {
            try {
                $groupItem = $this->iriConverter->getResourceFromIri($subject->groupItem);
            } catch (ItemNotFoundException|InvalidArgumentException) {
                return false;
            }
        }

        if (!$groupItem) {
            return false;
        }

        $trip = $groupItem->getTrip();
        switch ($attribute) {
            case self::CREATE:
                if ($user === $trip->getOwner() || $this->participationRepository->isUserAParticipantInTrip($user, $trip)) {
                    return true;
                }
                break;

            case self::DELETE:
                if($user === $assignment->getAssignedTo()){
                    return true;
                }
        }

        return false;
    }
}
