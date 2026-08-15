<?php

namespace App\Tests\assignment\api;

use App\Tests\assignment\AbstractAssignmentApiTestCase;

class DeleteAssignmentApiTest extends AbstractAssignmentApiTestCase
{
    public function testOwnerCanDeleteOwnAssignment(): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $user = $this->security->getUser();

        $groupItem = $this->groupItemRepository->findOneBy([
            'name' => 'Chaises'
        ]);

        $existingAssignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);

        $assignmentId = $existingAssignment->getId();

        $this->delete($assignmentId);

        $this->assertResponseStatusCodeSame(204);

        $this->entityManager->clear();

        $deletedAssignment = $this->assignmentRepository->find($assignmentId);

        $this->assertNull($deletedAssignment);
    }

    public function testNotAuthenticatedUserCannotDeleteAssignment(): void
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
        $existingAssignedQuantity = $existingAssignment->getAssignedQuantity();

        $this->delete($assignmentId);

        $this->assertResponseStatusCodeSame(401);

        $this->entityManager->clear();

        $assignment = $this->assignmentRepository->find($assignmentId);

        $this->assertNotNull($assignment);
        $this->assertSame(
            $existingAssignedQuantity,
            $assignment->getAssignedQuantity()
        );
    }

    public function testUserCannotDeleteAnotherUserAssignment(): void
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

        $this->delete($assignmentId);

        $this->assertResponseStatusCodeSame(403);

        $this->entityManager->clear();

        $assignment = $this->assignmentRepository->find($assignmentId);

        $this->assertNotNull($assignment);
        $this->assertSame(
            $existingAssignedQuantity,
            $assignment->getAssignedQuantity()
        );
    }

    public function testTripOwnerCannotDeleteAnotherUserAssignment(): void
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

        $this->delete($assignmentId);

        $this->assertResponseStatusCodeSame(403);

        $this->entityManager->clear();

        $assignment = $this->assignmentRepository->find($assignmentId);

        $this->assertNotNull($assignment);
    }
}
