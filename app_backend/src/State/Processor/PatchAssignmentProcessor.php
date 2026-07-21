<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Assignment;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PatchAssignmentProcessor extends CustomProcessor
{
    public function __construct(
        Security $security,
        private AssignmentRepository $assignmentRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct($security);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Assignment
    {
        $assignment = $this->assignmentRepository->findOneBy(['id' => $uriVariables['id']]);

        $newQty = $assignment->getAssignedQuantity() + $data->assignedQuantity;
        $assignment->setAssignedQuantity($newQty);

        $this->entityManager->flush();
        return $assignment;
    }
}
