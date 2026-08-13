<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[AssignmentAssert\AssignedQuantity]
readonly class AssignmentInputDTO
{
    public function __construct(
        #[Assert\Positive(message: "La quantité doit être supérieure à 0.")]
        public int    $assignedQuantity,

        #[Assert\NotBlank]
        public string $groupItem,
    )
    {
    }
}
