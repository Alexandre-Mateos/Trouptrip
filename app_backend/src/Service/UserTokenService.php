<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserTokenService
{
    private const TOKEN_LENGTH = 6;
    private const TOKEN_MIN_LIMIT = 0;
    private const TOKEN_NUMERIC_BASE = 10;

    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private EntityManagerInterface $em
    )
    {

    }
    public function generateUserToken(UserTokenTypeEnum $type, User $user): string
    {
        $this->revokeExistingValidToken($type, $user);

        $rawToken = $this->generateRawToken();

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

    private function generateRawToken(): string
    {
        $max = (pow(self::TOKEN_NUMERIC_BASE, self::TOKEN_LENGTH)-1);
        $randomNumber = random_int(self::TOKEN_MIN_LIMIT, $max);
        return str_pad($randomNumber, self::TOKEN_LENGTH, '0', STR_PAD_LEFT);
    }

    private function revokeExistingValidToken(UserTokenTypeEnum $type, User $user): void
    {
        if ($existingValidToken = $this->userTokenRepository->findValidTokenByUserAndType($user, $type)) {
            $existingValidToken->setExpiresAt(new \DateTimeImmutable());
        }
    }
}
