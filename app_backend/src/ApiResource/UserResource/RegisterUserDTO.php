<?php

namespace App\ApiResource\UserResource;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
