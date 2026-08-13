<?php

namespace App\ApiResource\AssignmentResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssignmentAssert;

#[Assert\GroupSequence(['AssignmentInputDTO', 'second'])]
#[AssignmentAssert\AssignedQuantity(groups:['second'])]
readonly class AssignmentInputDTO
{
    public function __construct(
        #[Assert\NotNull(message: 'Merci d\'indiquer une quantité.')]
        #[Assert\Positive(message: 'La quantité doit être supérieure à 0.')]
        public ?int $assignedQuantity,

        #[Assert\NotBlank(message: 'Merci d\'indiquer le matériel concerné.')]
        #[Assert\NotNull(message: 'L\'objet associé doit être renseigné')]
        public ?string $groupItem,
    ) {}
}
