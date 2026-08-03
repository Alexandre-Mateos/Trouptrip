<?php

namespace App\Factory;

use App\Entity\PersonalItem;
use App\Enum\GroupItemUnitEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PersonalItem>
 */
final class PersonalItemFactory extends PersistentObjectFactory
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
        return PersonalItem::class;
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
            'isPacked' => false,
            'name' => self::faker()->text(5),
            'owner' => UserFactory::new(),
            'quantity' => self::faker()->randomNumber(),
            'trip' => TripFactory::new(),
            'unit' => self::faker()->randomElement(GroupItemUnitEnum::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PersonalItem $personalItem): void {})
        ;
    }
}
