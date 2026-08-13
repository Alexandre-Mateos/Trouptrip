<?php

namespace App\Tests;

use App\Enum\ParticipationStatusEnum;
use App\Repository\AssignmentRepository;
use App\Repository\GroupItemRepository;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\SecurityBundle\Security;

class PostAssignmentTest extends AbstractApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultUrl = self::$ASSIGNMENTS;
    }

    public function testWithAcceptedParticipant(): void
    {
        $this->loginUser('poireau@test.fr', 'password');

        $groupItemRepository = $this->get(GroupItemRepository::class);
        $assignmentRepository = $this->get(AssignmentRepository::class);
        $security = $this->get(Security::class);

        $user = $security->getUser();


        $groupItem = $groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);
        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];
        $this->post($body);
        $this->assertResponseIsSuccessful();

        $assignment = $assignmentRepository->findOneBy(['groupItem' => $groupItem, 'assignedTo' => $user]);

        $this->assertNotNull($assignment);
        $this->assertSame($assignment->getAssignedQuantity(), 3);
        $this->assertFalse($assignment->getIsPacked());
    }

    public function testWithOwner(): void
    {
        $this->loginUser('duchamp@test.fr', 'password');

        $groupItemRepository = $this->get(GroupItemRepository::class);
        $assignmentRepository = $this->get(AssignmentRepository::class);
        $security = $this->get(Security::class);

        $user = $security->getUser();

        $groupItem = $groupItemRepository->findOneBy(['name' => 'Oranges']);
        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];
        $this->post($body);
        $this->assertResponseIsSuccessful();

        $assignment = $assignmentRepository->findOneBy(['groupItem' => $groupItem, 'assignedTo' => $user]);

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

        $groupItemRepository = $this->get(GroupItemRepository::class);

        $groupItem = $groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);
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
        $groupItemRepository = $this->get(GroupItemRepository::class);

        $groupItem = $groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);
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
        $groupItemRepository = $this->get(GroupItemRepository::class);

        $groupItem = $groupItemRepository->findOneBy(['name' => 'Bouteilles d\'eau']);
        $body = [
            "assignedQuantity" => 3,
            "groupItem" => "api/group_items/" . $groupItem->getId()
        ];
        $this->post($body);
        $this->assertResponseStatusCodeSame(409);
    }
}
