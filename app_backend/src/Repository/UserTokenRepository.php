<?php

namespace App\Repository;

use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserToken>
 */
class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }

    public function findValidToken(string $token): ?UserToken
    {
        return $this->createQueryBuilder('ut')
            ->addSelect('u')
            ->innerJoin('ut.requester', 'u')
            ->where('ut.token = :token')
            ->andWhere('ut.type = :type')
            ->andWhere('ut.expiresAt > :now')
            ->setParameter(':token', $token)
            ->setParameter(':type', UserTokenTypeEnum::CHECK_EMAIL)
            ->setParameter(':now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
