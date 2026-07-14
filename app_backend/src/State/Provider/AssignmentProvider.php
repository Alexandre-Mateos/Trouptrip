<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Assignment;
use App\Repository\AssignmentRepository;

class AssignmentProvider implements ProviderInterface
{
    public function __construct(
        private AssignmentRepository $assignmentRepository
    ){}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $tripId = $uriVariables["tripId"] ?? null;
        if(null === $tripId){
            return [];
        }

        return $this->assignmentRepository->getAssignmentsByTripId($tripId);
    }
}
