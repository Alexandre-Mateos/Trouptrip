<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[Assert\GroupSequence(['AssignmentUpdateDTO', 'second'])]
#[AssignmentAssert\AssignedQuantity(groups: ['second'])]
readonly class AssignmentUpdateDTO
{
    public function __construct(
        #[Assert\Positive(message: "La quantité doit être supérieure à 0.")]
        public ?int $assignedQuantity = null,

        public ?bool $isRemoval = null,

        public ?bool $isPacked = null,
    ) {}
}
