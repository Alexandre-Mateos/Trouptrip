<?php

namespace App\Validator;

use App\Repository\AssignmentRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AssignedQuantityValidator extends ConstraintValidator
{
    public function __construct(
        private AssignmentRepository $assignmentRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssignedQuantity) {
            throw new UnexpectedTypeException($constraint, AssignedQuantity::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $groupItem = $value->getGroupItem();

        $maxQuantity = $groupItem->getTotalQuantity();
        $alreadyAssignedQuantity = $this->assignmentRepository->getAssignedQuantityByGroupItemId($groupItem->getId());

        $remainingQuantity = $maxQuantity - $alreadyAssignedQuantity;
        $currentAssignedQuantity = $value->getAssignedQuantity();


        if($remainingQuantity < $currentAssignedQuantity){
            $this->context->buildViolation($constraint->wrongQuantity)
                ->atPath('assignedQuantity')
                ->setParameter('{{ available }}', $remainingQuantity)
                ->addViolation()
            ;
        }
    }
}
