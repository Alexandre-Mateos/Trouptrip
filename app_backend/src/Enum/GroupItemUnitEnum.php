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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
