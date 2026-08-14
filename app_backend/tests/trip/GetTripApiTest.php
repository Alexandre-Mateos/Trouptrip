<?php

namespace App\Tests\trip;

use App\Enum\ParticipationStatusEnum;
use PHPUnit\Framework\Attributes\TestWith;

class GetTripApiTest extends AbstractTripApiTestCase
{
    private const TRIP_TITLE = 'Trip de test trip';

    #[TestWith(['verreau@trip_test.fr'], 'Trip owner can access trip details')]
    #[TestWith(['chasse@trip_test.fr'], 'Accepted participant can access trip details')]
    public function testTripOwnerCanGetTripDetails(string $email): void
    {
        $this->loginUser($email, 'password');
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $this->defaultUrl =  self::$TRIPS . '/' . $tripId;
        $response = $this->get();
        $this->assertResponseStatusCodeSame(200);

        $data = $response->toArray();
        $this->assertSame($trip->getTitle(), $data['title']);
        $this->assertSame($trip->getDescription(), $data['description']);
        $this->assertEquals($trip->getStartDate(), new \DateTimeImmutable($data['startDate']));
        $this->assertEquals($trip->getEndDate(), new \DateTimeImmutable($data['endDate']));
        $this->assertSame($trip->getOwner()->getId(), $data['owner']['id']);

        $expectedParticipationIds = array_map(
            fn ($participation) => $participation->getId(),
            $trip->getParticipations()->toArray()
        );

        $returnedParticipationIds = array_map(
            fn ($participation) => $participation['id'],
            $data['participations']
        );

        $this->assertSame(sort($expectedParticipationIds), sort($returnedParticipationIds));

        $expectedGroupItemIds = array_map(
            fn ($groupItem) => $groupItem->getId(),
            $trip->getParticipations()->toArray()
        );

        $returnedGroupItemIds = array_map(
            fn ($groupItem) => $groupItem['id'],
            $data['groupItems']
        );

        $this->assertSame(sort($expectedGroupItemIds), sort($returnedGroupItemIds));
    }

    #[TestWith(['belanger@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::PENDING->value)]
    #[TestWith(['lapierre@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::DECLINED->value)]
    #[TestWith(['pichon@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::LEFT->value)]
    #[TestWith(['lepepin@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::EXCLUDED->value)]
    public function testCanNotGetTripDetails(string $email): void
    {
        $this->loginUser($email, 'password');
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $this->defaultUrl =  self::$TRIPS . '/' . $tripId;
        $this->get();
        $this->assertResponseStatusCodeSame(403);
    }

    public function testUnauthenticatedUserCanNotGetTripDetails(): void
    {
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $this->defaultUrl =  self::$TRIPS . '/' . $tripId;
        $this->get();
        $this->assertResponseStatusCodeSame(401);
    }
}
