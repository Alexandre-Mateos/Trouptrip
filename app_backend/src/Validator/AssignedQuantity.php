<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute()]
final class AssignedQuantity extends Constraint
{
    public string $wrongAddQuantity = 'Impossible d\'ajouter plus que la quantité encore disponible.';
    public string $wrongRemovalQuantity = 'La quantité à retirer doit être inférieure à la quantité assignée. Pour retirer toute l’assignation, utilisez la suppression.';
    public string $missingOperationType = 'Merci d\'indiquer le type d\'opération';
    public string $missingQuantity = 'Merci d\'indiquer une quantité';
    public string $emptyUpdate = 'Merci de renseigner des valeurs';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
