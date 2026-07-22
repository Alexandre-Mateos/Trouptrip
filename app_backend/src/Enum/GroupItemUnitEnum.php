<?php

namespace App\Enum;

enum GroupItemUnitEnum: string
{
    case GRAM = 'g';
    case KILOGRAM = 'kg';
    case MILILITER = 'ml';
    case CENTILITER = 'cl';
    case LITER = 'L';
    case PIECE = 'piece';

    public function label(): string
    {
        return match($this) {
            self::GRAM => 'Gramme (g)',
            self::KILOGRAM => 'Kilogramme (kg)',
            self::CENTILITER => 'Centilitre (cl)',
            self::LITER => 'Litre (L)',
            self::PIECE => 'Pièce',
        };
    }
}
