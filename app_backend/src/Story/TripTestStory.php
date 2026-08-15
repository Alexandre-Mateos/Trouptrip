<?php

namespace App\Story;

use App\Enum\GroupItemUnitEnum;
use App\Enum\ParticipationStatusEnum;
use App\Factory\GroupItemFactory;
use App\Factory\ParticipationFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class TripTestStory extends Story
{
    public function build(): void
    {
        $owner = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
            'email' => 'verreau@trip_test.fr',
            'firstname' => 'Oliver',
            'lastname' => 'Verreau',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
            'isVerified' => true,
        ]);

        // Création d'un utilisateur par status possible dans Participation
        $usersByStatus = [
            ParticipationStatusEnum::PENDING->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'belanger@trip_test.fr',
                'firstname' => 'Raymond',
                'lastname' => 'Bélanger',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::ACCEPTED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'chasse@trip_test.fr',
                'firstname' => 'Julien',
                'lastname' => 'Chasse',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::DECLINED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'lapierre@trip_test.fr',
                'firstname' => 'Carine',
                'lastname' => 'Lapierre',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::LEFT->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'pichon@trip_test.fr',
                'firstname' => 'Sophie',
                'lastname' => 'Pichon',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::EXCLUDED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'lepepin@trip_test.fr',
                'firstname' => 'Amanda',
                'lastname' => 'Lepepin',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),
        ];

        $randomUser = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
            'email' => 'brousse@trip_test.fr',
            'firstname' => 'Joseph',
            'lastname' => 'Brousse',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
            'isVerified' => true,
        ]);

        $trip = TripFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 11:00:00'),
            'owner' => $owner,
            'title' => 'Trip de test trip',
            'description' => 'Voyage utilisé pour tester la gestion des trip.',
            'startDate' => new \DateTimeImmutable('+1 month'),
            'endDate' => new \DateTimeImmutable('+1 month +7 days'),
            'isDeleted' => false,
        ]);

        foreach ($usersByStatus as $status => $user) {
            ParticipationFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 12:00:00'),
                'invitedBy' => $owner,
                'participant' => $user,
                'status' => ParticipationStatusEnum::from($status),
                'trip' => $trip,
            ]);
        }

        GroupItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 13:00:00'),
            'name' => 'Pommes de terre',
            'totalQuantity' => 15,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::KILOGRAM,
        ]);

        GroupItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 13:00:00'),
            'name' => 'Barbecue',
            'totalQuantity' => 1,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::PIECE,
        ]);

        // Trip auquel Verreau participe avec le status ACCEPTED
        $acceptedTrip = TripFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-02 11:00:00'),
            'owner' => $randomUser,
            'title' => 'Trip accepté pour Verreau',
            'description' => 'Voyage auquel Verreau participe avec le status ACCEPTED.',
            'startDate' => new \DateTimeImmutable('+2 months'),
            'endDate' => new \DateTimeImmutable('+2 months +7 days'),
            'isDeleted' => false,
        ]);

        ParticipationFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-02 12:00:00'),
            'invitedBy' => $randomUser,
            'participant' => $owner,
            'status' => ParticipationStatusEnum::ACCEPTED,
            'trip' => $acceptedTrip,
        ]);

        // Trip auquel Verreau n'a aucun lien
        TripFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-03 11:00:00'),
            'owner' => $randomUser,
            'title' => 'Trip sans lien avec Verreau',
            'description' => 'Voyage qui ne doit pas apparaître dans la collection de Verreau.',
            'startDate' => new \DateTimeImmutable('+3 months'),
            'endDate' => new \DateTimeImmutable('+3 months +7 days'),
            'isDeleted' => false,
        ]);

        // Trip appartenant à Verreau mais supprimé
        TripFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-04 11:00:00'),
            'owner' => $owner,
            'title' => 'Trip supprimé de Verreau',
            'description' => 'Voyage supprimé qui ne doit pas apparaître dans la collection.',
            'startDate' => new \DateTimeImmutable('+4 months'),
            'endDate' => new \DateTimeImmutable('+4 months +7 days'),
            'isDeleted' => true,
        ]);
    }
}
