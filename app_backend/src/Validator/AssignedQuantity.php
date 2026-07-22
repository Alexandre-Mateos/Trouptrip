<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class AssignedQuantity extends Constraint
{
    public string $wrongAddQuantity = 'Impossible d\'ajouter plus que la quantité encore disponible. Quantité restante disponible : {{ available }}.';
    public string $wrongRemovalQuantity = 'Impossible de retirer plus que la quantité déjà assigné. QUantité déjà assignée: {{ alreadyAssignedQuantity }}.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
