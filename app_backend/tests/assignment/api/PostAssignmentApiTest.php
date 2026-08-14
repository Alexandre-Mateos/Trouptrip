<?php

namespace App\Tests\assignment\api;

use App\Enum\ParticipationStatusEnum;
use App\Tests\assignment\AbstractAssignmentApiTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class PostAssignmentApiTest extends AbstractAssignmentApiTestCase
{
    public function testWithAcceptedParticipant(): void
    {
        $this->loginUser('poireau@test.fr', 'password');
        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);

        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];

        $this->post($body);
        $this->assertResponseIsSuccessful();

        $assignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);

        $this->assertNotNull($assignment);
        $this->assertSame($assignment->getAssignedQuantity(), 3);
        $this->assertFalse($assignment->getIsPacked());
    }

    public function testWithOwner(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');
        $user = $this->security->getUser();
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Oranges']);

        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];

        $this->post($body);
        $this->assertResponseIsSuccessful();

        $assignment = $this->assignmentRepository->findOneBy([
            'groupItem' => $groupItem,
            'assignedTo' => $user
        ]);

        $this->assertNotNull($assignment);
        $this->assertSame($assignment->getAssignedQuantity(), 3);
        $this->assertFalse($assignment->getIsPacked());
    }

    #[TestWith(['lapioche@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::PENDING->value)]
    #[TestWith(['bertrand@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::DECLINED->value)]
    #[TestWith(['lachaise@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::LEFT->value)]
    #[TestWith(['johnson@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::EXCLUDED->value)]
    public function testWithNotAcceptedParticipant(
        string $email
    ): void
    {
        $this->loginUser($email, 'password');
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);

        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];

        $this->post($body);
        $this->assertResponseStatusCodeSame(403);
    }

    #[TestWith([3, 201], 'Assigned quantity inferior to the remaining quantity')]
    #[TestWith([6, 201], 'Assigned quantity equal to the remaining quantity')]
    #[TestWith([7, 422], 'Assigned quantity superior to the remaining quantity')]
    #[TestWith([null, 422], 'Assigned quantity null')]
    #[TestWith([-1, 422], 'Assigned quantity negative')]
    public function testAssignedQuantity(
        ?int $quantityToAssigned,
        int $expectedCode
    ): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);

        $body = [
            "assignedQuantity" => $quantityToAssigned,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];

        $this->post($body);
        $this->assertResponseStatusCodeSame($expectedCode);
    }

    public function testUserWithExistingAssignment(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);

        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];

        $this->post($body);
        $this->assertResponseStatusCodeSame(409);
    }

    #[TestWith([''], 'Test with blank string')]
    #[TestWith([null], 'Test with null')]
    public function testWithForbiddenGroupItemValue(
        ?string $groupItem
    ): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $body = [
            "assignedQuantity" => 3,
            "groupItem" => $groupItem,
        ];

        $this->post($body);
        $this->assertResponseStatusCodeSame(403);
    }
}
