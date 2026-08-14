<?php

namespace App\Validator;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\AssignmentResource\AssignmentInputDTO;
use App\ApiResource\AssignmentResource\AssignmentUpdateDTO;
use App\Entity\GroupItem;
use App\Repository\AssignmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AssignedQuantityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly AssignmentRepository $assignmentRepository,
        private readonly IriConverterInterface $iriConverter,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssignedQuantity) {
            throw new UnexpectedTypeException($constraint, AssignedQuantity::class);
        }

        if ($value === null) {
            return;
        }


        if ($value instanceof AssignmentInputDTO) {
            if (
                $value->groupItem === null ||
                $value->groupItem === '' ||
                $value->assignedQuantity === null
            ) {
                return;
            }

            /** @var GroupItem $groupItem */
            $groupItem = $this->iriConverter->getResourceFromIri($value->groupItem);

            $this->validateAddition(
                $groupItem,
                $value->assignedQuantity,
                $constraint
            );

            return;
        }

        if ($value instanceof AssignmentUpdateDTO) {


            if ($value->assignedQuantity === null) {
                return;
            }

            $request = $this->requestStack->getCurrentRequest();
            $assignmentId = $request?->attributes->get('id');

            if (!$assignmentId) {
                return;
            }

            $assignment = $this->assignmentRepository->findOneBy(['id' => $assignmentId]);

            if (!$assignment) {
                return;
            }

            $groupItem = $assignment->getGroupItem();

            if ($value->isRemoval === true) {
                $user = $this->security->getUser();

                $alreadyAssignedQuantityByUser =
                    $this->assignmentRepository
                        ->getAssignedQuantityByUserIdAndGroupItemId(
                            $user->getId(),
                            $groupItem->getId()
                        );


                if (
                    $alreadyAssignedQuantityByUser < $value->assignedQuantity
                ) {
                    $this->context
                        ->buildViolation($constraint->wrongRemovalQuantity)
                        ->atPath('assignedQuantity')
                        ->setParameter(
                            '{{ alreadyAssignedQuantity }}',
                            $alreadyAssignedQuantityByUser
                        )
                        ->addViolation();

                }elseif ($alreadyAssignedQuantityByUser === $value->assignedQuantity) {
                    $this->context
                        ->buildViolation($constraint->shouldRemove)
                        ->atPath('assignedQuantity')
                        ->addViolation();
                }

                return;
            }

            $this->validateAddition(
                $groupItem,
                $value->assignedQuantity,
                $constraint
            );
        }
    }

    private function validateAddition(
        GroupItem $groupItem,
        int $quantity,
        AssignedQuantity $constraint
    ): void {
        $maxQuantity = $groupItem->getTotalQuantity();

        $alreadyAssignedQuantity =
            $this->assignmentRepository
                ->getAssignedQuantityByGroupItemId($groupItem->getId());

        $remainingQuantity =
            $maxQuantity - $alreadyAssignedQuantity;

        if ($remainingQuantity < $quantity) {
            $this->context
                ->buildViolation($constraint->wrongAddQuantity)
                ->atPath('assignedQuantity')
                ->setParameter('{{ available }}', $remainingQuantity)
                ->addViolation();
        }
    }
}
