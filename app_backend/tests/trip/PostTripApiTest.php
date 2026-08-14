<?php

namespace App\Tests\trip;

class PostTripApiTest extends AbstractTripApiTestCase
{
    private const BODY = [
        'title' => 'Trip de test',
        'description' => 'Trip de test',
        'startDate' => '2027-10-01',
        'endDate' => '2027-10-08',
    ];
    public function testAuthenticatedUserCanCreateTrip(): void
    {
        $this->loginUser('chasse@trip_test.fr', 'password');

        $user = $this->security->getUser();

        $this->post(self::BODY);
        $this->assertResponseStatusCodeSame(201);

        $trip = $this->tripRepository->findOneBy(['title' => 'Trip de test']);

        $this->assertNotNull($trip);
        $this->assertSame($user->getId(), $trip->getOwner()->getId());
        $this->assertFalse($trip->isDeleted());
    }

    public function testNotAuthenticatedUserCannotCreateTrip(): void
    {
        $this->post(self::BODY);
        $this->assertResponseStatusCodeSame(401);
        $trip = $this->tripRepository->findOneBy(['title' => 'Trip de test']);

        $this->assertNull($trip);
    }
}
