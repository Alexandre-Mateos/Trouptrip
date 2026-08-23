<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class UniqueParticipation extends Constraint
{
    public string $message = 'Une invitation est déjà en cours pour l\'adresse {{ value }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
