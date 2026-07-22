<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[AssignmentAssert\AssignedQuantity]
readonly class AssignmentInputDTO
{
    public function __construct(
        #[Assert\NotNull(message: "Merci d'indiquer une quantité")]
        #[Assert\NotEqualTo(value: 0, message: "La quantité à modifier ne peut pas être égale à 0.")]
        public int $assignedQuantity,
        public ?string $groupItem,
        public bool $isRemoval
    ){}
}
