<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[AssignmentAssert\AssignedQuantity]
readonly class AssignmentUpdateDTO
{
    public function __construct(
        #[Assert\NotEqualTo(value: 0, message: "La quantité à modifier ne peut pas être égale à 0.")]
        public ?int $assignedQuantity = null,
        public ?bool $isRemoval = null,
        public ?bool $isPacked = null,
    ){}
}
