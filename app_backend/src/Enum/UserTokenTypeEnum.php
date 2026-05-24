<?php

namespace App\Enum;

enum UserTokenTypeEnum: string
{
    const CHECK_EMAIL = 'CHECK_EMAIL';

    const RESET_PASSWORD = 'RESET_PASSWORD';
}
