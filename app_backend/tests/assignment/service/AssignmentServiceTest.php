<?php

namespace App\Tests\assignment\service;

use App\Repository\GroupItemRepository;
use App\Repository\UserRepository;
use App\Service\AssignmentService;
use App\Tests\AbstractKernelTestCaseTest;
use PHPUnit\Framework\Attributes\TestWith;

class AssignmentServiceTest extends AbstractKernelTestCaseTest
{
    private AssignmentService $assignmentService;
    private GroupItemRepository $groupItemRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assignmentService = $this->get(AssignmentService::class);
        $this->groupItemRepository = $this->get(GroupItemRepository::class);
        $this->userRepository = $this->get(UserRepository::class);
    }

    #[TestWith([3, true])]
    #[TestWith([4, true])]
    #[TestWith([5, false])]
    public function testCanAddQty(
        int $quantity,
        bool $expected
    ): void
    {
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);
        $result = $this->assignmentService->canAddQty($groupItem, $quantity);

        $this->assertSame($expected, $result);
    }

    #[TestWith([3, true])]
    #[TestWith([4, false])]
    #[TestWith([5, false])]
    public function testCanSubtractQty(
        int $quantity,
        bool $expected
    ): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'poireau@test.fr']);
        $groupItem = $this->groupItemRepository->findOneBy(['name' => 'Chaises']);
        $result = $this->assignmentService->canSubtractQty($user, $groupItem, $quantity);

        $this->assertSame($expected, $result);
    }
}
