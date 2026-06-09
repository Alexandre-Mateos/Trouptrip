<?php

namespace App\ApiResource\UserResource;

readonly class OutputUserMeDTO
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public string $email,
        public \DateTimeImmutable $createdAt,
    )
    {
    }
}
