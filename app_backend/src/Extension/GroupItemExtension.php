<?php

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\GroupItem;
use App\Enum\ParticipationStatusEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;

final readonly class GroupItemExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->filterOnParticipant($queryBuilder, $resourceClass);
    }

    public function filterOnParticipant(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (GroupItem::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(sprintf('%s.trip', $rootAlias), 't')
            ->leftJoin('t.participations', 'p')
            ->andWhere('t.owner = :user OR (p.participant = :user AND p.status = :status)')
            ->setParameter('user', $user)
            ->setParameter('status', ParticipationStatusEnum::ACCEPTED);

    }

}
