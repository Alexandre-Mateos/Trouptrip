<?php

namespace App\ApiResource\SecurityResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\Security\ResetPasswordProcessor;

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
        public string $token,
        public string $plainPassword
    )
    {
    }
}
