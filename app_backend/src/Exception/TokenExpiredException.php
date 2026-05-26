<?php

namespace App\Exception;

class TokenExpiredException extends \RuntimeException
{
    public function __construct(string $message = 'Lien expiré ou déjà utilisé', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
