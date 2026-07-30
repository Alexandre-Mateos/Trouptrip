<?php

namespace App\State\Processor;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract readonly class CustomProcessor implements ProcessorInterface
{
    public function __construct(
        protected Security                 $security,
    )
    {
    }

    protected function getUser(): User
    {
        $user = $this->security->getUser();
        if(!$user instanceof User){
            throw new AccessDeniedException();
        }
        return $user;
    }
}
