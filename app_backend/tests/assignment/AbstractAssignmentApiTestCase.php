<?php

namespace App\Tests\assignment;

use App\Repository\AssignmentRepository;
use App\Repository\GroupItemRepository;
use App\Repository\UserRepository;
use App\Tests\AbstractApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AbstractAssignmentApiTestCase extends AbstractApiTestCase
{
    protected AssignmentRepository $assignmentRepository;
    protected GroupItemRepository $groupItemRepository;
    protected UserRepository $userRepository;
    protected EntityManagerInterface $entityManager;
    protected Security $security;

    static string $ASSIGNMENTS = '/api/assignments';

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultUrl = self::$ASSIGNMENTS;

        $this->assignmentRepository = $this->getEntity(AssignmentRepository::class);
        $this->groupItemRepository = $this->getEntity(GroupItemRepository::class);
        $this->userRepository = $this->getEntity(UserRepository::class);
        $this->entityManager = $this->getEntity(EntityManagerInterface::class);
        $this->security = $this->getEntity(Security::class);
    }
}
