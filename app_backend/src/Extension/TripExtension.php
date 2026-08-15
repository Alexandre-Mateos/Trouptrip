<?php

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Trip;
use App\Enum\ParticipationStatusEnum;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class TripExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {

        if (
            Trip::class !== $resourceClass
            || $this->security->isGranted('ROLE_ADMIN')
            || null === $this->security->getUser()
        ) {
            return;
        }

        $this->addParticipantWhere($queryBuilder, $resourceClass);
        $this->addIsDeletedWhere($queryBuilder, $resourceClass);
    }


    private function addParticipantWhere(QueryBuilder $queryBuilder): void
    {
        $user = $this->security->getUser();

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->leftJoin(sprintf('%s.participations', $rootAlias), 'p')
            ->andWhere(sprintf('%s.owner = :user OR (p.participant = :user AND p.status = :status)', $rootAlias))
            ->setParameter('user', $user)
            ->setParameter('status', ParticipationStatusEnum::ACCEPTED);
    }

    private function addIsDeletedWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.isDeleted = :isDeleted', $rootAlias))
            ->setParameter('isDeleted', false);
    }
}
