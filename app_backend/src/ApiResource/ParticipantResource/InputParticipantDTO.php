<?php

namespace App\ApiResource\ParticipantResource;

use Symfony\Component\Validator\Constraints as Assert;
readonly class InputParticipantDTO
{
    public function __construct(
        #[Assert\NotBlank(
            message: 'Merci d\'inqiquer une adresse email'
        )]
        #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
        public string $email
    )
    {
    }
}
