<?php

namespace App\Enum;

enum TripMemberStatusEnum: string
{
    case LEFT = 'LEFT';
    case EXCLUDED = 'EXCLUDED';
    case ACTIVE = 'ACTIVE';
}
