<?php

namespace App\Tests;

use App\Enum\ParticipationStatusEnum;
use App\Repository\AssignmentRepository;
use App\Repository\GroupItemRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\SecurityBundle\Security;

class PatchAssignmentTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultUrl = self::$ASSIGNMENTS;
    }

    #[TestWith([3, false, 200, 7], 'Addition: Qty inferior to the remaininng')]
    #[TestWith([4, false, 200, 8], 'Addition: Qty same as remaining')]
    #[TestWith([5, false, 422, 4], 'Addition: Qty superior to the remainin')]
    #[TestWith([3, true, 200, 1], 'Subtraction: Qty inferior to the already assigned qty')]
    #[TestWith([4, true, 422, 4], 'Subtraction: Qty same as the already assigned qty')]
    #[TestWith([5, true, 422, 4], 'Subtraction: Qty superior to the already assigned qty')]
    public function testUpdateQuantityWithAcceptedParticipant(
        int $qty,
        bool $isRemoval,
        int $expectedCode,
        int $expectedAssignedQty
    ): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $groupItemRepository = $this->get(GroupItemRepository::class);
        $assignmentRepository = $this->get(AssignmentRepository::class);
        $security = $this->get(Security::class);
        $entityManager = $this->get(EntityManagerInterface::class);

        $user = $security->getUser();
        $groupItem = $groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $assignmentRepository->findOneBy(['groupItem' => $groupItem, 'assignedTo' => $user]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => $qty,
            "isRemoval" => $isRemoval
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame($expectedCode);

        $entityManager->clear();

        $updatedAssignment = $assignmentRepository->find($assignmentId);

        $this->assertSame(
            $expectedAssignedQty,
            $updatedAssignment->getAssignedQuantity()
        );
    }
}
