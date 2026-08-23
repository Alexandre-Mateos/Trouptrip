<?php

namespace App\ApiResource\ParticipationResource;

use App\Enum\ParticipationStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateParticipationStatusDTO
{
    public function __construct(
        #[Assert\Choice(
            callback: [ParticipationStatusEnum::class, 'values'],
            message: "L'option n'est pas valide"
        )]
        #[Assert\NotBlank]
        public string $status,
    ){}
}
