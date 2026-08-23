<?php

namespace App\ApiResource\SecurityResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\Security\ResetPasswordProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Reset Password',
    operations: [
        new Post(
            uriTemplate: '/reset_password',
            output: false,
            processor: ResetPasswordProcessor::class
        )
    ]
)]
class ResetPasswordInputDTO
{
    public function __construct(
        public string $email,
        public string $token,

        #[Assert\NotBlank]
        #[Assert\NotCompromisedPassword]
        #[Assert\PasswordStrength(
            message: 'Le mot de passe choisi n\'est pas assez fort'
        )]
        public string $plainPassword
    )
    {
    }
}
