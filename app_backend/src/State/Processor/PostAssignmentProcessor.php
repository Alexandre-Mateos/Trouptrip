<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Assignment;
use App\Entity\GroupItem;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PostAssignmentProcessor extends CustomProcessor
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
        /** @var GroupItem $groupItem */
        $groupItem = $this->iriConverter->getResourceFromIri($data->groupItem);

        $assignment = new Assignment();
        $assignment->setAssignedQuantity($data->assignedQuantity)
            ->setIsPacked(false)
            ->setGroupItem($groupItem)
            ->setAssignedTo($currentUser);

        return $this->persistProcessor->process($assignment, $operation, $uriVariables, $context);
    }
}
