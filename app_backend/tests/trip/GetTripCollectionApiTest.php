<?php

namespace App\Tests\trip;

class GetTripCollectionApiTest extends AbstractTripApiTestCase
{
    public function testUserGetsOnlyAccessibleTrips(): void
    {
        $this->loginUser('verreau@trip_test.fr', 'password');
        $this->defaultUrl = self::$TRIPS;

        $data = $this->getDataCollection();

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $data);

        $returnedTitles = array_map(
            fn (array $trip) => $trip['title'],
            $data
        );

        $expectedTitles = [
            'Trip de test trip',
            'Trip accepté pour Verreau',
        ];

        $this->assertSame(
            sort($expectedTitles),
            sort($returnedTitles)
        );
        $this->assertNotContains(
            'Trip sans lien avec Verreau',
            $returnedTitles
        );

        $this->assertNotContains(
            'Trip supprimé de Verreau',
            $returnedTitles
        );
    }

    public function testNotAuthenticatedUserCannotGetTripCollection(): void
    {
        $this->defaultUrl = self::$TRIPS;
        $this->get();
        $this->assertResponseStatusCodeSame(401);
    }
}
