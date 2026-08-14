<?php

namespace App\Tests\assignment\api;

use App\Entity\Trip;
use App\Enum\ParticipationStatusEnum;
use App\Repository\TripRepository;
use App\Tests\assignment\AbstractAssignmentApiTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class GetAssignmentCollectionApiTest extends AbstractAssignmentApiTestCase
{
    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $tripRepository = $this->getEntity(TripRepository::class);
        $this->trip = $tripRepository->findOneBy([
            'title' => 'Trip de test Assignment'
        ]);;
    }

    #[TestWith(['duchamp@test.fr'], 'TripOwner can access assignment collection')]
    #[TestWith(['poireau@test.fr'], 'Accepted participant can access assignment collection')]
    public function testAccessToAssignmentCollection(
        string $email
    ): void
    {
        $this->loginUser($email, 'password');
        $tripId = $this->trip->getId();
        $existingAssignments = $this->assignmentRepository->getAssignmentsByTripId($tripId);

        $this->defaultUrl = '/api/trips/' . $tripId . '/assignments';

        $data = $this->getDataCollection();

        $this->assertResponseIsSuccessful();
        $this->assertCount(count($existingAssignments), $data);

        $expectedIds = array_map(
            fn ($assignment) => $assignment->getId(),
            $existingAssignments
        );

        $returnedIds = array_map(
            fn (array $assignment) => $assignment['id'],
            $data
        );

        sort($expectedIds);
        sort($returnedIds);

        $this->assertSame($expectedIds, $returnedIds);
    }

    #[TestWith(['lapioche@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::PENDING->value)]
    #[TestWith(['bertrand@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::DECLINED->value)]
    #[TestWith(['lachaise@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::LEFT->value)]
    #[TestWith(['johnson@test.fr'], 'User with Participation at status ' . ParticipationStatusEnum::EXCLUDED->value)]
    public function testNotAcceptedParticipantCannotGetAssignmentsCollection(
        string $email
    ): void
    {
        $this->loginUser($email, 'password');
        $this->defaultUrl = '/api/trips/' . $this->trip->getId() . '/assignments';

        $this->get();
        $this->assertResponseStatusCodeSame(403);
    }



    public function testNotAuthenticatedUserCannotGetAssignmentsCollection(): void
    {
        $this->defaultUrl = '/api/trips/' . $this->trip->getId() . '/assignments';

        $this->get();
        $this->assertResponseStatusCodeSame(401);
    }
}
