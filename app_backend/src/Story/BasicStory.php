<?php

namespace App\Story;

use App\Enum\GroupItemUnitEnum;
use App\Enum\ParticipationStatusEnum;
use App\Factory\GroupItemFactory;
use App\Factory\ParticipationFactory;
use App\Factory\PersonalItemFactory;
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

        $groupItemData = [
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 10:00:00'),
                'name' => 'Tente 4 places',
                'totalQuantity' => 1,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 10:15:00'),
                'name' => 'Pâtes',
                'totalQuantity' => 3,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::KILOGRAM,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-12 11:00:00'),
                'name' => 'Eau minérale',
                'totalQuantity' => 6,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::LITER,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-13 09:30:00'),
                'name' => 'Sac de couchage',
                'totalQuantity' => 3,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-13 14:00:00'),
                'name' => 'Café moulu',
                'totalQuantity' => 500,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::GRAM,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-14 08:00:00'),
                'name' => 'Réchaud à gaz',
                'totalQuantity' => 2,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-14 16:45:00'),
                'name' => 'Huile d\'olive',
                'totalQuantity' => 750,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::MILILITER,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 10:20:00'),
                'name' => 'Trousse de secours',
                'totalQuantity' => 1,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 11:00:00'),
                'name' => 'Riz',
                'totalQuantity' => 2,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::GRAM,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 18:30:00'),
                'name' => 'Lampe torche',
                'totalQuantity' => 3,
                'trip' => TripFactory::new(),
                'unit' => GroupItemUnitEnum::PIECE,
            ],
        ];

        $personalItemData = [
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 11:00:00'),
                'isPacked'  => false,
                'name' => 'brosse à dents',
                'quantity' => 1,
                'unit'  => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 11:00:00'),
                'isPacked'  => false,
                'name' => 'Harry Potter',
                'quantity' => 1,
                'unit'  => GroupItemUnitEnum::PIECE,
            ],
            [
                'createdAt' => new \DateTimeImmutable('2026-06-15 11:00:00'),
                'isPacked'  => false,
                'name' => 'Lunettes de soleil',
                'quantity' => 1,
                'unit'  => GroupItemUnitEnum::PIECE,
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
                'isDeleted' => false
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

            foreach ($groupItemData as $groupItem) {
                GroupItemFactory::createOne([
                    'createdAt' => new $groupItem['createdAt'],
                    'name' => $groupItem['name'],
                    'totalQuantity' => $groupItem['totalQuantity'],
                    'trip' => $associatedTrip,
                    'unit' => $groupItem['unit'],
                ]);
            }

            foreach ($personalItemData as $personalItem) {
                PersonalItemFactory::createOne([
                    'createdAt' => new $personalItem['createdAt'],
                    'isPacked'  => $personalItem['isPacked'],
                    'name' => $personalItem['name'],
                    'owner' => $userAlexandre,
                    'quantity' => $personalItem['quantity'],
                    'trip' => $associatedTrip,
                    'unit' => $personalItem['unit'],
                ]);
            }
        }
    }
}
