<?php

namespace App\ApiResource\AssignmentResource;

readonly class AssignmentInputDTO
{
    public function __construct(
        public int $assignedQuantity
    ){}
}
