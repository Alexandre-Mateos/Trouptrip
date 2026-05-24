<?php

namespace App\ApiResource\UserResource;

readonly class OutputUserDTO
{
    public function __construct(
        public string $email,
        public string $firstname,
        public string $lastname
    )
    {
    }
}
