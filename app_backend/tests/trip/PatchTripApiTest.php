<?php

namespace App\Tests\trip;

use App\Entity\Trip;
use App\Enum\ParticipationStatusEnum;
use PHPUnit\Framework\Attributes\TestWith;

class PatchTripApiTest extends AbstractTripApiTestCase
{
    private const TRIP_TITLE = 'Trip de test trip';
    private const UPDATED_TRIP_TITLE = 'Trip de test trip après modification';

    public function testTripOwnerCanUpdateTrip(): void
    {
        $this->loginUser('verreau@trip_test.fr', 'password');

        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();

        $body = ['title' => self::UPDATED_TRIP_TITLE];
        $this->patch($body, $tripId);

        $this->assertResponseIsSuccessful();

        $updatedTrip = $this->refreshTestTrip($tripId);
        $this->assertSame(self::UPDATED_TRIP_TITLE, $updatedTrip->getTitle());
    }

    public function testNotAuthenticatedUserCannotUpdateTrip(): void
    {
        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();

        $body = ['title' => self::UPDATED_TRIP_TITLE];
        $this->patch($body, $tripId);

        $this->assertResponseStatusCodeSame(401);

        $updatedTrip = $this->refreshTestTrip($tripId);
        $this->assertSame(self::TRIP_TITLE, $updatedTrip->getTitle());
    }

    #[TestWith(['belanger@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::PENDING->value)]
    #[TestWith(['chasse@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::ACCEPTED->value)]
    #[TestWith(['lapierre@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::DECLINED->value)]
    #[TestWith(['pichon@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::LEFT->value)]
    #[TestWith(['lepepin@trip_test.fr'], 'Participant with status ' . ParticipationStatusEnum::EXCLUDED->value)]
    public function testParticipantCannotUpdateTrip(
        string $email
    ): void
    {
        $this->loginUser($email, 'password');

        $trip = $this->getTestTrip(self::TRIP_TITLE);
        $tripId = $trip->getId();
        $body = ['title' => self::UPDATED_TRIP_TITLE];
        $this->patch($body, $tripId);

        $this->assertResponseStatusCodeSame(403);
        $updatedTrip = $this->refreshTestTrip($tripId);

        $this->assertSame(self::TRIP_TITLE, $updatedTrip->getTitle());
    }
}
