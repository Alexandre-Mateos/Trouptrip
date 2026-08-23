<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Assignment;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PatchAssignmentProcessor implements ProcessorInterface
{
    public function __construct(
        private AssignmentRepository $assignmentRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Assignment
    {
        $assignment = $this->assignmentRepository->findOneBy(['id' => $uriVariables['id']]);
        if(null !== $data->isRemoval && null !== $data->assignedQuantity){
            if($data->isRemoval){
                $newQty = $assignment->getAssignedQuantity() - $data->assignedQuantity;
            }else{
                $newQty = $assignment->getAssignedQuantity() + $data->assignedQuantity;
            }
            $assignment->setAssignedQuantity($newQty);
        }

        if(null !== $data->isPacked && $data->isPacked !== $assignment->getIsPacked()){
            $assignment->setIsPacked($data->isPacked);
        }

        $this->entityManager->flush();
        return $assignment;
    }
}
