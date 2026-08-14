<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class AssignedQuantity extends Constraint
{
    public string $wrongAddQuantity = 'Impossible d\'ajouter plus que la quantité encore disponible. Quantité restante disponible : {{ available }}.';
    public string $wrongRemovalQuantity = 'Impossible de retirer plus que la quantité déjà assigné. QUantité déjà assignée: {{ alreadyAssignedQuantity }}.';
    public string $shouldRemove = 'Vous allez tomber à 0. Supprimez plutôt votre contribution';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
