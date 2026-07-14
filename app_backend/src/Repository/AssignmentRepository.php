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

    public function getAssignmentsByTripId(int $tripId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.groupItem', 'g')
            ->leftJoin('g.trip', 't')
            ->where('t.id = :id')
            ->setParameter('id', $tripId)
            ->getQuery()
            ->getResult();
    }
}
