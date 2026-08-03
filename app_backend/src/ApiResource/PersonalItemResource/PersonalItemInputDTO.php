<?php

namespace App\ApiResource\PersonalItemResource;

use App\Enum\GroupItemUnitEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PersonalItemInputDTO
{
    public function __construct(
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: "Le nom doit faire au moins {{ limit }} caractères.",
            maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
        )]
        public string $name,
        #[Assert\NotNull(message: "Merci d'indiquer une quantité")]
        #[Assert\Positive(message: "La quantité doit être supérieure à 0")]
        public int    $quantity,
        #[Assert\NotNull]
        public string $trip,
        #[Assert\NotBlank(message: "L'unité est requise")]
        #[Assert\NotNull]
        #[Assert\Choice(
            callback: [GroupItemUnitEnum::class, 'values'],
            message: "L'unité choisie n'est pas valide."
        )]
        public string $unit
    )
    {
    }
}
