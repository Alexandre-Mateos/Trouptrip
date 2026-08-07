<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class ExistingUser extends Constraint
{
    public string $message = 'L\'adresse {{ value }} ne correspond à aucun compte vérifié.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
