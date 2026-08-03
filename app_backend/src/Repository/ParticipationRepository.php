<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\Trip;
use App\Entity\User;
use App\Enum\ParticipationStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Participation>
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function isUserAParticipantInTrip(UserInterface $user, Trip $trip): bool
    {
        $count = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.trip = :trip')
            ->andWhere('p.participant = :user')
            ->andWhere('p.status = :status')
            ->setParameter('trip', $trip)
            ->setParameter('user', $user)
            ->setParameter('status', ParticipationStatusEnum::ACCEPTED)
            ->getQuery()
            ->getSingleScalarResult();
        return (int) $count > 0;
    }

}
