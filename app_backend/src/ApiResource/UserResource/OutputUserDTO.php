<?php

namespace App\ApiResource\UserResource;

use Symfony\Component\Serializer\Attribute\Groups;

readonly class OutputUserDTO
{
    public function __construct(
        #[Groups(['trip:item', 'assignment:collection', 'participation:collection'])]
        public int $id,
        #[Groups(['trip:item', 'participation:collection'])]
        public string $firstname,
        #[Groups(['trip:item', 'participation:collection'])]
        public string $lastname
    )
    {
    }
}
