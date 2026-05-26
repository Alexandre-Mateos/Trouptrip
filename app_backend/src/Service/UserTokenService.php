<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserTokenService
{
    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private EntityManagerInterface $em
    )
    {

    }
    public function generateUserToken(UserTokenTypeEnum $type, User $user): string
    {
        $this->revokeExistingValidToken($type, $user);

        $rawToken = bin2hex(random_bytes(32));

        $userToken = new UserToken()
            ->setRequester($user)
            ->setToken(hash('sha256', $rawToken))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setType($type->value)
            ->setExpiresAt(new \DateTimeImmutable()->modify($type->getExpirationTime()));

        $this->em->persist($userToken);
        $this->em->flush();

        return $rawToken;
    }

    private function revokeExistingValidToken(UserTokenTypeEnum $type, User $user): void
    {
        if ($existingValidToken = $this->userTokenRepository->findValidTokenByUserAndType($user, $type)) {
            $existingValidToken->setExpiresAt(new \DateTimeImmutable());
        }
    }
}
