<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Participation;
use App\Enum\ParticipationStatusEnum;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PatchParticipationProcessor implements ProcessorInterface
{
    public function __construct(
        private ParticipationRepository  $participationRepository,
        private EntityManagerInterface $entityManager,
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Participation
    {
        $participation = $this->participationRepository->findOneBy(['id' => $uriVariables['id']]);
        $newStatus = ParticipationStatusEnum::from($data->status);
        if($newStatus !== $participation->getStatus()) {
            $participation->setStatus($newStatus);
        }
        $this->entityManager->flush();
        return $participation;
    }
}
