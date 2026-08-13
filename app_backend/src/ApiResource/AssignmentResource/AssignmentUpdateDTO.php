<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[AssignmentAssert\AssignedQuantity]
readonly class AssignmentUpdateDTO
{
    public function __construct(
        #[Assert\Positive(message: "La quantité doit être supérieure à 0.")]
        public ?int $assignedQuantity = null,

        #[Assert\When(
            expression: 'this.assignedQuantity !== null',
            constraints: [
                new Assert\NotNull(
                    message: 'Merci d’indiquer s’il s’agit d’un ajout ou d’un retrait.'
                )
            ]
        )]
        public ?bool $isRemoval = null,

        public ?bool $isPacked = null,
    ) {}
}
