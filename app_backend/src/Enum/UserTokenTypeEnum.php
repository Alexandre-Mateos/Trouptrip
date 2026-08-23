<?php

namespace App\Enum;

enum UserTokenTypeEnum: string
{
    case CHECK_EMAIL = 'CHECK_EMAIL';
    case RESET_PASSWORD = 'RESET_PASSWORD';

    public function getExpirationTime():string
    {
        return match($this){
            self::CHECK_EMAIL => '+1 hour',
            self::RESET_PASSWORD => '+ 15 minutes'
        };
    }
    }
