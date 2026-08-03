<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

class DeleteTripProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->setIsDeleted(true);
        $this->em->persist($data);
        $this->em->flush();
    }
}
