<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\Processor\CheckEmailProcessor;
use Symfony\Flex\Unpack\Operation;

#[ApiResource(
    shortName: 'Check Email',
    operations: [
        new Post(
            uriTemplate: '/check_email',
            output: false,
            processor: CheckEmailProcessor::class
        )
    ]
)]
readonly class CheckEmailDTO
{
    public function __construct(
        public string $token
    )
    {
    }
}
