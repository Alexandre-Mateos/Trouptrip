<?php

namespace App\Exception;

class VerifiedUserException extends \RuntimeException
{
    public function __construct(string $message = 'Compte utilisateur déjà vérifié', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
