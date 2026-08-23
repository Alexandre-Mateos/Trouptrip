<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ExistingUserValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var ExistingUser $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $isVerifiedUserEmail = $this->userRepository->isVerifiedUserEmail($value->email);

        if(!$isVerifiedUserEmail){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->email)
                ->atPath('email')
                ->addViolation()
            ;
        }
    }
}
