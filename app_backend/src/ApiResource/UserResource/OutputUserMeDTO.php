<?php

namespace App\ApiResource\UserResource;

readonly class OutputUserMeDTO
{
    public function __construct(
        public int $id,
        public string $firstname,
        public string $lastname,
        public string $email,
        public \DateTimeImmutable $createdAt,
    )
    {
    }
}
