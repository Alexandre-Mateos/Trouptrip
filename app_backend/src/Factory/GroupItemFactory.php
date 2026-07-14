<?php

namespace App\Factory;

use App\Entity\GroupItem;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<GroupItem>
 */
final class GroupItemFactory extends PersistentObjectFactory
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
        return GroupItem::class;
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
            'name' => self::faker()->text(5),
            'totalQuantity' => self::faker()->randomNumber(),
            'trip' => TripFactory::new(),
            'unit' => self::faker()->randomElement(['kg', 'g', 'unité', 'L', 'ml']),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(GroupItem $groupItem): void {})
        ;
    }
}
