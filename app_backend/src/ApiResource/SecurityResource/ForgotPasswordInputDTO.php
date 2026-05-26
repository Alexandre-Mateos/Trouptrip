<?php

namespace App\ApiResource\SecurityResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\Security\ForgotPasswordProcessor;

use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Forgot Password',
    operations: [
        new Post(
            uriTemplate: '/forgot_password',
            output: false,
            processor: ForgotPasswordProcessor::class
        )
    ]
)]
class ForgotPasswordInputDTO
{
    public function __construct(

        #[Assert\NotBlank]
        #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
        public string $email,
    )
    {
    }
}
