<?php

namespace App\Repository;

use App\Entity\Assignment;
use App\Entity\GroupItem;
use App\Entity\User;
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

    public function getAssignedQuantityByGroupItem(GroupItem $groupItem): int
    {
        return $this->createQueryBuilder('a')
            ->select('COALESCE(SUM(a.assignedQuantity), 0)')
            ->where('a.groupItem = :groupItem')
            ->setParameter('groupItem', $groupItem)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAssignedQuantityByUserAndGroupItem(User $user, GroupItem $groupItem): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COALESCE(SUM(a.assignedQuantity), 0)')
            ->where('a.groupItem = :groupItem')
            ->andWhere('a.assignedTo = :user')
            ->setParameter('groupItem', $groupItem)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function hasAssignmentForUserAndGroupItem(GroupItem $groupItem, User $user): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('Count(a.id)')
            ->where('a.groupItem = :groupItem')
            ->andWhere('a.assignedTo = :user')
            ->setParameter('groupItem', $groupItem)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
