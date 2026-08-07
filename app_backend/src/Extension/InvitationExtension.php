<?php

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Participation;
use App\Enum\ParticipationStatusEnum;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class InvitationExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {

        if (null === $operation || $operation->getName() !== 'get_me_invitations') {
            return;
        }
        $this->addUserMeWhere($queryBuilder, $resourceClass);
    }

    private function addUserMeWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Participation::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $currentUser = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->leftJoin(sprintf('%s.trip', $rootAlias), 't')
            ->andWhere(sprintf('%s.participant = :currentUser', $rootAlias))
            ->andWhere(sprintf('%s.status = :status', $rootAlias))
            ->andWhere('t.endDate >= :now')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('status', ParticipationStatusEnum::PENDING)
            ->setParameter('now', new \DateTime());
    }
}
