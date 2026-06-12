<?php

namespace App\Factory;

use App\Entity\Trip;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Trip>
 */
final class TripFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Trip::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => new \DateTimeImmutable(),
            'endDate' => new \DateTimeImmutable('+1 week'),
            'owner' => UserFactory::new(),
            'startDate' => new \DateTimeImmutable('+1 month'),
            'title' => self::faker()->text(20),
            'description' =>self::faker()->text(100)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Trip $trip): void {})
        ;
    }
}
