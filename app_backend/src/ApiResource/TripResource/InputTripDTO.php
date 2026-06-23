<?php

namespace App\ApiResource\TripResource;

use App\ApiResource\ParticipantResource\InputParticipantDTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class InputTripDTO
{
    public function __construct(
        #[Assert\NotBlank(
            message: 'Merci d\'indiquer un titre'
        )]
        public string             $title,
        public ?string            $description,
        #[Assert\NotBlank(
            message: 'Merci d\'indiquer une date de début'
        )]
        #[Assert\Date]
        public string $startDate,
        #[Assert\NotBlank(
            message: 'Merci d\'indiquer une date de fin'
        )]
        #[Assert\Date]
        public string $endDate
    )
    {
    }
}
