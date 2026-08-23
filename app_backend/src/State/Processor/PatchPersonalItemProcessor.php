<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalItem;
use App\Enum\GroupItemUnitEnum;
use App\Repository\PersonalItemRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PatchPersonalItemProcessor implements ProcessorInterface
{
    public function __construct(
        private PersonalItemRepository $personalItemRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PersonalItem
    {
        $personalItem = $this->personalItemRepository->findOneBy(['id' => $uriVariables['id']]);

        if ($data->name !== null && $data->name !== $personalItem->getName()) {
            $personalItem->setName($data->name);
        }

        if ($data->quantity !== null && $data->quantity !== $personalItem->getQuantity()) {
            $personalItem->setQuantity($data->quantity);
        }

        if ($data->unit !== null) {
            $unitEnum = GroupItemUnitEnum::from($data->unit);
            if ($unitEnum !== $personalItem->getUnit()) {
                $personalItem->setUnit($unitEnum);
            }
        }

        if($data->isPacked !== null && $data->isPacked !== $personalItem->getIsPacked()){
            $personalItem->setIsPacked($data->isPacked);
        }

        $this->entityManager->flush();

        return $personalItem;
    }
}
