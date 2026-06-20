<?php

namespace App\ApiResource\TripResource;

use App\ApiResource\ParticipantResource\InputParticipantDTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class InputTripDTO
{
    public function __construct(
        public string             $title,
        public ?string            $description,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
        /**
         * @var InputParticipantDTO[]
         */
        #[Assert\Valid]
        public ?array             $participants
    )
    {
    }
}
