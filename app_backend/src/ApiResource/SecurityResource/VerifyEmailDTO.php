<?php

namespace App\ApiResource\SecurityResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\VerifyEmailProcessor;

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
        public string $token
    )
    {
    }
}
