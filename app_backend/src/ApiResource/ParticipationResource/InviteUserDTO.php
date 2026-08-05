<?php

namespace App\ApiResource\ParticipationResource;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[AppAssert\ExistingUser]
#[AppAssert\UniqueParticipation]
readonly class InviteUserDTO
{
    public function __construct(
        #[Assert\NotBlank(
            message: 'Merci d\'inqiquer une adresse email'
        )]
        #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
        public string $email,
        #[Assert\NotBlank]
        public string $trip
    )
    {
    }
}
