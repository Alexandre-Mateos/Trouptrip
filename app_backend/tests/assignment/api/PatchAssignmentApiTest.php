<?php

namespace App\Tests\assignment\api;

use App\Tests\assignment\AbstractAssignmentApiTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class PatchAssignmentApiTest extends AbstractAssignmentApiTestCase
{
    #[TestWith([3, false, 200, 7], 'Addition: Qty inferior to the remaining')]
    #[TestWith([4, false, 200, 8], 'Addition: Qty same as remaining')]
    #[TestWith([5, false, 422, 4], 'Addition: Qty superior to the remaining')]
    #[TestWith([3, true, 200, 1], 'Subtraction: Qty inferior to the already assigned qty')]
    #[TestWith([4, true, 422, 4], 'Subtraction: Qty same as the already assigned qty')]
    #[TestWith([5, true, 422, 4], 'Subtraction: Qty superior to the already assigned qty')]
    public function testUpdateQuantity(
        int $qty,
        bool $isRemoval,
        int $expectedCode,
        int $expectedAssignedQty
    ): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => $qty,
            "isRemoval" => $isRemoval
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame($expectedCode);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame($expectedAssignedQty, $updatedAssignment->getAssignedQuantity());
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    #[TestWith([0, false, 4])]
    #[TestWith([-1, false, 4])]
    #[TestWith([null, false, 4])]
    #[TestWith([3, null, 4])]
    #[TestWith([null, null, 4])]
    public function testUpdateQuantityWithInvalidData(
        ?int $qty,
        ?bool $isRemoval,
        int $expectedAssignedQty
    ): void {
        $this->loginUser('poireau@test.fr', 'password');

        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => $qty,
            "isRemoval" => $isRemoval
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(422);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame($expectedAssignedQty, $updatedAssignment->getAssignedQuantity());
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    public function testUpdateWithNotAuthenticatedUser(): void
    {
        $existingUser = $this->userRepository->findOneBy([
            'email' => 'poireau@test.fr'
        ]);

        $groupItem = $this->groupItemRepository->findOneBy([
            'name' => 'Chaises'
        ]);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $existingUser
        ]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => 3,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(401);

        $this->entityManager->clear();
        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame(4, $updatedAssignment->getAssignedQuantity());
    }

    public function testUpdateOnForbiddenAssignment(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $existingUser = $this->userRepository->findOneBy(['email' => 'poireau@test.fr']);
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);
        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $existingUser
        ]);

        $assignmentId = $existingAssignment->getId();
        $body = [
            "assignedQuantity" => 3,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(403);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame(
            4,
            $updatedAssignment->getAssignedQuantity()
        );
    }

    public function testTripOwnerCanUpdateOwnAssignment(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);

        $assignmentId = $existingAssignment->getId();

        $body = [
            "assignedQuantity" => 1,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(200);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame(
            3,
            $updatedAssignment->getAssignedQuantity()
        );
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    public function testTripOwnerCannotUpdateAnotherUserAssignment(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $existingUser = $this->userRepository->findOneBy([
            'email' => 'poireau@test.fr'
        ]);

        $groupItem = $this->groupItemRepository->findOneBy([
            'name' => 'Chaises'
        ]);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $existingUser
        ]);

        $assignmentId = $existingAssignment->getId();
        $existingAssignedQuantity = $existingAssignment->getAssignedQuantity();

        $body = [
            "assignedQuantity" => 1,
            "isRemoval" => false
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(403);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame(
            $existingAssignedQuantity,
            $updatedAssignment->getAssignedQuantity()
        );
        $this->assertFalse($updatedAssignment->getIsPacked());
    }

    public function testUpdateWithEmptyBody(): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);

        $assignmentId = $existingAssignment->getId();
        $existingAssignedQuantity = $existingAssignment->getAssignedQuantity();

        $body = [];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame(422);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame(
            $existingAssignedQuantity,
            $updatedAssignment->getAssignedQuantity()
        );
        $this->assertFalse($updatedAssignment->getIsPacked());
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

        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);
        $assignmentId = $existingAssignment->getId();

        $body = [
            "isPacked" => $isPacked
        ];

        $this->patch($body, $assignmentId);
        $this->assertResponseStatusCodeSame($expectedCode);

        $this->entityManager->clear();

        $updatedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertSame($expectedIsPacked, $updatedAssignment->getIsPacked());
        $this->assertSame(4, $updatedAssignment->getAssignedQuantity());
    }
}
