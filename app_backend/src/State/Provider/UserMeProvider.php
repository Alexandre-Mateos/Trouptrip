<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserResource\OutputUserMeDTO;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class UserMeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
//        renvoyer quelques stats utiles comme le nombre de jour d'adhésion du membre,
//        ou le nombre de séjour auxquels il à participé. Ou encore le nombre de jours depuis
//        le dernier séjour. A calculer dans le back car données statiques

        return new OutputUserMeDTO(
            $user->getId(),
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getCreatedAt()
        );
    }
}
