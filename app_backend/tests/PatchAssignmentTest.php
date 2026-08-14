<?php

namespace App\Tests;

use App\Repository\AssignmentRepository;
use App\Repository\GroupItemRepository;
use App\Repository\UserRepository;
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

        $this->assertSame($expectedAssignedQty, $updatedAssignment->getAssignedQuantity());
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    #[TestWith([0, false, 4])]
    #[TestWith([-1, false, 4])]
    #[TestWith([null, false, 4])]
    #[TestWith([3, null, 4])]
    #[TestWith([null, null, 4])]
    public function testUpdateQuantityWithWrongDTO(
        ?int $qty,
        ?bool $isRemoval,
        int $expectedAssignedQty
    ): void{
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
        $this->assertResponseStatusCodeSame(422);

        $entityManager->clear();

        $updatedAssignment = $assignmentRepository->find($assignmentId);

        $this->assertSame($expectedAssignedQty, $updatedAssignment->getAssignedQuantity());
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    public function testUpdateWithNotAuthenticatedUser(): void
    {
        $groupItemRepository = $this->get(GroupItemRepository::class);
        $assignmentRepository = $this->get(AssignmentRepository::class);
        $userRepository = $this->get(UserRepository::class);
        $entityManager = $this->get(EntityManagerInterface::class);

        $existingUser = $userRepository->findOneBy(['email' => 'poireau@test.fr']);
        $groupItem = $groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $assignmentRepository->findOneBy(['groupItem' => $groupItem, 'assignedTo' => $existingUser]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => 3,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(401);

        $entityManager->clear();

        $updatedAssignment = $assignmentRepository->find($assignmentId);

        $this->assertSame(
            4,
            $updatedAssignment->getAssignedQuantity()
        );
    }

    public function testUpdateOnForbiddenAssignment(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $groupItemRepository = $this->get(GroupItemRepository::class);
        $assignmentRepository = $this->get(AssignmentRepository::class);
        $userRepository = $this->get(UserRepository::class);
        $entityManager = $this->get(EntityManagerInterface::class);

        $existingUser = $userRepository->findOneBy(['email' => 'poireau@test.fr']);
        $groupItem = $groupItemRepository->findOneBy(['name' => 'Chaises']);
        $existingAssignment = $assignmentRepository->findOneBy(['groupItem' => $groupItem, 'assignedTo' => $existingUser]);

        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => 3,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(403);

        $entityManager->clear();

        $updatedAssignment = $assignmentRepository->find($assignmentId);

        $this->assertSame(
            4,
            $updatedAssignment->getAssignedQuantity()
        );
    }

    #[TestWith([true, true, 200])]
    #[TestWith([null, false, 422])]
    public function testUpdateIsPacked(
        ?bool $isPacked,
        bool $expectedIsPacked,
        int $expectedCode
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
            "isPacked" => $isPacked
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame($expectedCode);

        $entityManager->clear();

        $updatedAssignment = $assignmentRepository->find($assignmentId);

        $this->assertSame($expectedIsPacked, $updatedAssignment->getIsPacked());
        $this->assertSame(4, $updatedAssignment->getAssignedQuantity());
    }
}
