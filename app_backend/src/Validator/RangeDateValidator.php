<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class RangeDateValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RangeDate) {
            throw new UnexpectedTypeException($constraint, RangeDate::class);
        }

        if (null === $value->getStartDate() || null === $value->getEndDate()) {
            return;
        }

        $today = new \DateTimeImmutable();

        if($value->getStartDate() < $today){
            $this->context->buildViolation($constraint->wrongStartDate)
                ->atPath('startDate')
                ->addViolation()
            ;
        }

        if(
            $value->getEndDate() < $value->getStartDate()
        ){
            $this->context->buildViolation($constraint->wrongEndDate)
                ->atPath('endDate')
                ->addViolation()
            ;
        }
    }
}
