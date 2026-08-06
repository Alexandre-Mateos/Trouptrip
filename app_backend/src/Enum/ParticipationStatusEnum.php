<?php

namespace App\Enum;

enum ParticipationStatusEnum: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case DECLINED = 'DECLINED';
    case LEFT = 'LEFT';
    case CANCELLED = 'CANCELLED';
    case EXCLUDED = 'EXCLUDED';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
