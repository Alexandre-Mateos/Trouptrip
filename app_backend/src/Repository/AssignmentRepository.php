<?php

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function getAssignmentsByTripId(int $tripId): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.groupItem', 'g')
            ->leftJoin('g.trip', 't')
            ->addSelect('g', 't')
            ->where('t.id = :id')
            ->setParameter('id', $tripId)
            ->getQuery()
            ->getResult();
    }

    public function getAssignedQuantityByGroupItemId(int $groupId): int
    {
        return $this->createQueryBuilder('a')
            ->select('COALESCE(SUM(a.assignedQuantity), 0)')
            ->leftJoin('a.groupItem', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $groupId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAssignedQuantityByUserIdAndGroupItemId(int $userId, int $groupItemId): int
    {
        return $this->createQueryBuilder('a')
            ->select('COALESCE(SUM(a.assignedQuantity), 0)')
            ->leftJoin('a.groupItem', 'g')
            ->leftJoin('a.assignedTo', 'u')
            ->where('g.id = :groupItemId')
            ->andWhere('u.id = :userId')
            ->setParameter('groupItemId', $groupItemId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
