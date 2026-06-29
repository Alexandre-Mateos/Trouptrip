<?php

namespace App\Story;

use App\Enum\ParticipationStatusEnum;
use App\Factory\ParticipationFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class BasicStory extends Story
{
    public function build(): void
    {
        $userAlexandre = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
            'email' => 'alexandre.mateos@mail.com',
            'firstname' => 'Alexandre',
            'isVerified' => true,
            'lastname' => 'Mateos',
            'password' => password_hash('alexandre', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
        ]);

        $userJulia = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
            'email' => 'julia.lorenzo@mail.com',
            'firstname' => 'Julia',
            'isVerified' => true,
            'lastname' => 'Lorenzo',
            'password' => password_hash('julia', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
        ]);

        $userMarie = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
            'email' => 'marie.bergamotte@mail.com',
            'firstname' => 'Marie',
            'isVerified' => true,
            'lastname' => 'Bergamotte',
            'password' => password_hash('marie', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
        ]);

        $trips = [
            [
                'title' => 'Week-end à Rome',
                'description' => 'Découverte du Colisée, du Vatican et des spécialités italiennes.',
                'start' => '+1 week',
                'end' => '+10 days',
            ],
            [
                'title' => 'Road Trip en Écosse',
                'description' => 'Parcours des Highlands entre châteaux, lacs et paysages sauvages.',
                'start' => '+3 weeks',
                'end' => '+5 weeks',
            ],
            [
                'title' => 'Escapade à Barcelone',
                'description' => 'Quelques jours entre plage, tapas et architecture moderniste.',
                'start' => '+6 weeks',
                'end' => '+7 weeks',
            ],
            [
                'title' => 'Aventure en Islande',
                'description' => 'Observation des volcans, cascades et sources d’eau chaude.',
                'start' => '+2 months',
                'end' => '+10 weeks',
            ],
            [
                'title' => 'Découverte de Tokyo',
                'description' => 'Immersion dans la culture japonaise entre temples et quartiers animés.',
                'start' => '+3 months',
                'end' => '+15 weeks',
            ],
            [
                'title' => 'Séjour en Grèce',
                'description' => 'Visite des îles grecques et détente au bord de la mer Égée.',
                'start' => '+4 months',
                'end' => '+18 weeks',
            ],
            [
                'title' => 'Randonnée dans les Alpes',
                'description' => 'Une semaine sportive à travers les plus beaux sentiers alpins.',
                'start' => '+5 months',
                'end' => '+22 weeks',
            ],
            [
                'title' => 'Découverte du Maroc',
                'description' => 'Entre médinas, désert et montagnes de l’Atlas.',
                'start' => '+6 months',
                'end' => '+27 weeks',
            ],
            [
                'title' => 'Voyage au Canada',
                'description' => 'Exploration du Québec et des grands espaces naturels.',
                'start' => '+8 months',
                'end' => '+35 weeks',
            ],
            [
                'title' => 'Circuit en Thaïlande',
                'description' => 'Temples, marchés flottants et plages paradisiaques.',
                'start' => '+10 months',
                'end' => '+44 weeks',
            ],
        ];


        $paticipantsData = [
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
                'email' => 'jean.papon@mail.com',
                'firstname' => 'Jean',
                'isVerified' => true,
                'lastname' => 'Papon',
                'password' => password_hash('jean', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
                'email' => 'camille.dupont@mail.com',
                'firstname' => 'Camille',
                'isVerified' => true,
                'lastname' => 'Dupont',
                'password' => password_hash('camille', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 00:00:00'),
                'email' => 'paul.tiredon@mail.com',
                'firstname' => 'Paul',
                'isVerified' => true,
                'lastname' => 'Tiredon',
                'password' => password_hash('paul', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
            ]
        ];

        $participants = array_map( function($user){
            return UserFactory::createOne($user);
        } ,$paticipantsData);

        foreach ($trips as $trip) {

            $associatedTrip = TripFactory::createOne([
                'createdAt' => new \DateTimeImmutable(),
                'owner' => $userAlexandre,
                'title' => $trip['title'],
                'description' => $trip['description'],
                'startDate' => new \DateTimeImmutable($trip['start']),
                'endDate' => new \DateTimeImmutable($trip['end']),
                'isDeleted' => true
            ]);

            foreach ($participants as $participant) {

                ParticipationFactory::createOne([
                    'createdAt' => new \DateTimeImmutable('2026-06-14 00:00:00'),
                    'invitedBy' => $userAlexandre,
                    'participant' => $participant,
                    'status' => ParticipationStatusEnum::ACCEPTED,
                    'trip' => $associatedTrip
                ]);

            }
        }
    }
}
