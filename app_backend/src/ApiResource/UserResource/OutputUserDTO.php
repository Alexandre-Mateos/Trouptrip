<?php

namespace App\ApiResource\UserResource;

use Symfony\Component\Serializer\Attribute\Groups;

readonly class OutputUserDTO
{
    public function __construct(
        #[Groups(['trip:item'])]
        public int $id,
        public string $email,
        #[Groups(['trip:item'])]
        public string $firstname,
        #[Groups(['trip:item'])]
        public string $lastname
    )
    {
    }
}
