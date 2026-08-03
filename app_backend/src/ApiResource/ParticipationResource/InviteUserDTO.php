<?php

namespace App\ApiResource\ParticipationResource;

readonly class InviteUserDTO
{
    public function __construct(
        public string $email,
        public string $trip
    )
    {
    }
}
