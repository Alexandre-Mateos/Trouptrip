<?php

namespace App\Tests\trip;

use App\Enum\ParticipationStatusEnum;
use PHPUnit\Framework\Attributes\TestWith;

class DeleteTripApiTest extends AbstractTripApiTestCase
{
    private const TRIP_TITLE = 'Trip de test trip';
    public function testTripOwnerCanDeleteTrip(): void
    {
        $this->loginUser('verreau@trip_test.fr', 'password');
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $this->delete($tripId);

        $this->assertResponseStatusCodeSame(204);

        $deletedTrip = $this->refreshTestTrip($tripId);

        $this->assertNotNull($deletedTrip);
        $this->assertTrue($deletedTrip->isDeleted());
    }

    public function testNotAuthenticatedUserCannotDeleteTrip(): void
    {
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $this->delete($tripId);

        $this->assertResponseStatusCodeSame(401);

        $existingTrip = $this->refreshTestTrip($tripId);

        $this->assertNotNull($existingTrip);
        $this->assertFalse($existingTrip->isDeleted());
    }

    #[TestWith(['belanger@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::PENDING->value)]
    #[TestWith(['chasse@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::ACCEPTED->value)]
    #[TestWith(['lapierre@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::DECLINED->value)]
    #[TestWith(['pichon@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::LEFT->value)]
    #[TestWith(['lepepin@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::EXCLUDED->value)]
    public function testParticipantCannotDeleteTrip(
        string $email
    ): void
    {
        $this->loginUser($email, 'password');

        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();

        $this->delete($tripId);

        $this->assertResponseStatusCodeSame(403);
        $existingTrip = $this->refreshTestTrip($tripId);

        $this->assertNotNull($existingTrip);
        $this->assertFalse($existingTrip->isDeleted());
    }
}
