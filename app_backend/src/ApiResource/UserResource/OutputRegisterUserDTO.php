<?php

namespace App\ApiResource\UserResource;

readonly class OutputRegisterUserDTO
{
    public function __construct(
        public string $email,
        public string $firstname,
        public string $lastname
    ) {}
}
