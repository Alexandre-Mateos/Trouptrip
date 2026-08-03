<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class RangeDate extends Constraint
{
    public string $wrongStartDate = 'Impossible de démarrer un séjour dans le passé';
    public string $wrongEndDate = 'Date de début et de fin inversés';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
