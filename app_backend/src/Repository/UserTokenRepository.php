<?php

namespace App\Repository;

use App\Entity\User;
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

    public function findValidTokenByTypeAndEmail(string $token, UserTokenTypeEnum $type, string $email): ?UserToken
    {
        return $this->createQueryBuilder('ut')
            ->addSelect('u')
            ->innerJoin('ut.requester', 'u')
            ->where('ut.token = :token')
            ->andWhere('ut.type = :type')
            ->andWhere('u.email = :email')
            ->andWhere('ut.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('type', $type->value)
            ->setParameter('email', $email)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findValidTokenByUserAndType(User $user, UserTokenTypeEnum $type): ?UserToken
    {
        return $this->createQueryBuilder('ut')
            ->where('ut.requester = :requester')
            ->andWhere('ut.type = :type')
            ->andWhere('ut.expiresAt > :now')
            ->setParameter('requester', $user)
            ->setParameter('type', $type->value)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
