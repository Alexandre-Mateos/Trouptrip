<?php

namespace App\ApiResource\UserResource;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank(
            message: 'Merci d\'inqiquer une adresse email'
        )]
        #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
        public ?string $email,

        #[Assert\NotBlank(
            message: 'Merci d\'inqiquer un mot de passe'
        )]
        #[Assert\NotCompromisedPassword(
            message: 'Ce mot de passe à été compromis par le passé. Choisis en un autre'
        )]
        #[Assert\PasswordStrength(
            message: 'Le mot de passe n\'est pas assez fort'
        )]
        public ?string $plainPassword,

        #[Assert\NotBlank(
            message: 'Merci d\'indiquer ton prénom'
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: '50 caractères autorisés',
        )]
        public ?string $firstname,

        #[Assert\NotBlank(
            message: 'Merci d\'indiquer ton nom'
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: '50 caractères autorisés',
        )]
        public ?string $lastname
    )
    {
    }
}
