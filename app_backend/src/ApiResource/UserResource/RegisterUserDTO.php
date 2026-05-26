<?php

namespace App\ApiResource\UserResource;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
        public ?string $email,

        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        #[Assert\PasswordStrength(
            message: 'Le mot de passe n\'est pas assez fort'
        )]
        public ?string $plainPassword,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 50,
            maxMessage: '50 caractères autorisés',
        )]
        public ?string $firstname,

        #[Assert\NotBlank]
        #[Assert\Length(
            max: 50,
            maxMessage: '50 caractères autorisés',
        )]
        public ?string $lastname
    )
    {
    }
}
