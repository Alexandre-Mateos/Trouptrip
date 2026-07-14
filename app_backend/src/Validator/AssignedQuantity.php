<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class AssignedQuantity extends Constraint
{
    public string $wrongQuantity = 'Vous ne pouvez pas vous assigner plus d\'items que disponibles. Quantité restante disponible : {{ available }}.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
