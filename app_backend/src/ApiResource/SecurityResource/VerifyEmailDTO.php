<?php

namespace App\ApiResource\SecurityResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\VerifyEmailProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Check Email',
    operations: [
        new Post(
            uriTemplate: '/verify_email',
            output: false,
            processor: VerifyEmailProcessor::class
        )
    ]
)]
readonly class VerifyEmailDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        public string $token
    ) {}
}
