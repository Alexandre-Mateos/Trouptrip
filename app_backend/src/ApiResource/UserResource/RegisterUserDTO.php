<?php

namespace App\ApiResource\UserResource;
readonly class RegisterUserDTO
{
    public function __construct(
        public ?string $email,
        public ?string $plainPassword,
        public ?string $firstname,
        public ?string $lastname
    )
    {
    }
}
