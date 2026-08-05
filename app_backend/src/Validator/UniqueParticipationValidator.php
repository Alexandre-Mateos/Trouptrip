<?php

namespace App\Validator;

use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Trip;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueParticipationValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ParticipationRepository $participationRepository,
        private readonly IriConverterInterface $iriConverter,
        private readonly UserRepository $userRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var UniqueParticipation $constraint */

        if (null === $value || empty($value->email) || empty($value->trip)) {
            return;
        }

        try {
            /** @var Trip $trip */
            $trip = $this->iriConverter->getResourceFromIri($value->trip);
        } catch (\Throwable) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $value->email]);
        if (!$user) {
            return;
        }

        $isUserAlreadyInvited = $this->participationRepository->isUserAlreadyInvited($user, $trip);

        if($isUserAlreadyInvited){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->email)
                ->atPath('email')
                ->addViolation()
            ;
        }
    }
}
