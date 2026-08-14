<?php

namespace App\Validator;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\AssignmentResource\AssignmentInputDTO;
use App\ApiResource\AssignmentResource\AssignmentUpdateDTO;
use App\Entity\GroupItem;
use App\Entity\User;
use App\Repository\AssignmentRepository;
use App\Service\AssignmentService;
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
        private readonly AssignmentService $assignmentService,
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

            if(!$this->assignmentService->canAddQty($groupItem, $value->assignedQuantity)) {
                $this->buildViolation($constraint->wrongAddQuantity, 'assignedQuantity');
            }
            return;
        }

        if ($value instanceof AssignmentUpdateDTO) {

            // DTO vide
            if (
                $value->assignedQuantity === null
                && $value->isRemoval === null
                && $value->isPacked === null
            ) {
                $this->buildViolation($constraint->emptyUpdate);
                return;
            }

            // DTO avec Quantité mais sans ajout ou retrait
            if (
                $value->assignedQuantity !== null
                && $value->isRemoval === null
            ) {

                $this->buildViolation($constraint->missingOperationType, 'isRemoval');
                return;
            }

            // DTO avec ajout/retrait mais sans quantité
            if (
                $value->assignedQuantity === null
                && $value->isRemoval !== null
            ) {
                $this->buildViolation($constraint->missingQuantity, 'assignedQuantity');
                return;
            }

            // Uniquement isPacked, on laise passer
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

                if (!$user instanceof User) {
                    return;
                }

                if (!$this->assignmentService->canSubtractQty(
                    $user,
                    $groupItem,
                    $value->assignedQuantity
                )) {
                    $this->buildViolation(
                        'assignedQuantity',
                        $constraint->wrongRemovalQuantity
                    );
                }
                return;
            }

            if (!$this->assignmentService->canAddQty(
                $groupItem,
                $value->assignedQuantity
            )) {
                $this->buildViolation(
                    'assignedQuantity',
                    $constraint->wrongAddQuantity
                );
            }
        }
    }

    private function buildViolation(string $message, ?string $path = null, ): void
    {
        $violation = $this->context->buildViolation($message);
        if ($path !== null) {
            $violation->atPath($path);
        }
        $violation->addViolation();
    }
}
