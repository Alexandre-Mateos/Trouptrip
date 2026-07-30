<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalItem;
use App\Entity\Trip;
use App\Enum\GroupItemUnitEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PersonalItemProcessor extends CustomProcessor
{
    public function __construct(
        Security                   $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private IriConverterInterface $iriConverter,
    )
    {
        parent::__construct($security);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $currentUser = $this->getUser();

        /** @var Trip $trip */
        $trip = $this->iriConverter->getResourceFromIri($data->trip);

        $personalItem = new PersonalItem();
        $personalItem->setName($data->name);
        $personalItem->setQuantity($data->quantity);
        $personalItem->setUnit(GroupItemUnitEnum::from($data->unit));
        $personalItem->setTrip($trip);

        $personalItem->setOwner($currentUser);
        $personalItem->setIsPacked(false);

        return $this->persistProcessor->process($personalItem, $operation, $uriVariables, $context);
    }
}
