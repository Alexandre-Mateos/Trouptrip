<?php

namespace App\Service;

use App\Entity\GroupItem;
use App\Entity\User;
use App\Repository\AssignmentRepository;

readonly class AssignmentService
{
    public function __construct(
        private  AssignmentRepository $assignmentRepository,
    )
    {
    }

    public function canAddQty(
        GroupItem $groupItem,
        int $quantity,
    ): bool {

        $maxQuantity = $groupItem->getTotalQuantity();
        $alreadyAssignedQuantity = $this->assignmentRepository->getAssignedQuantityByGroupItem($groupItem);

        return $quantity <= $maxQuantity - $alreadyAssignedQuantity ;
    }

    public function canSubtractQty(
        User $user,
        GroupItem $groupItem,
        int $quantity,
    ): bool {

        $alreadyAssignedQuantityByUser = $this->assignmentRepository->getAssignedQuantityByUserAndGroupItem($user, $groupItem);

        return $quantity < $alreadyAssignedQuantityByUser;
    }
}

