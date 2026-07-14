<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PostAssignmentProcessor extends CustomProcessor
{
    public function __construct(
        Security                       $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    )
    {
        parent::__construct($security);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $currentUser = $this->getUser();
        $data->setAssignedTo($currentUser);
        $data->setIsPacked(false);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
