<?php

namespace App\Story;

use App\Enum\GroupItemUnitEnum;
use App\Enum\ParticipationStatusEnum;
use App\Factory\AssignmentFactory;
use App\Factory\GroupItemFactory;
use App\Factory\ParticipationFactory;
use App\Factory\PersonalItemFactory;
use App\Factory\TripFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class AssignmentTestStory extends Story
{
    public function build(): void
    {
//        Propriétaire du Trip
        $owner = UserFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
            'email' => 'duchamp@test.fr',
            'firstname' => 'Alexandre',
            'lastname' => 'Duchamp',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'roles' => ['ROLE_USER'],
            'isVerified' => true,
        ]);

//        Création d'un utilisateur par status possible dans Participation
        $usersByStatus = [
            ParticipationStatusEnum::PENDING->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'lapioche@test.fr',
                'firstname' => 'Paul',
                'lastname' => 'Lapioche',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::ACCEPTED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'poireau@test.fr',
                'firstname' => 'Alice',
                'lastname' => 'Poireau',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::DECLINED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'bertrand@test.fr',
                'firstname' => 'David',
                'lastname' => 'Bertrand',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::LEFT->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'lachaise@test.fr',
                'firstname' => 'Lucie',
                'lastname' => 'Lachaise',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),

            ParticipationStatusEnum::EXCLUDED->value => UserFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 10:00:00'),
                'email' => 'johnson@test.fr',
                'firstname' => 'Emma',
                'lastname' => 'Johnson',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER'],
                'isVerified' => true,
            ]),
        ];


        $trip = TripFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 11:00:00'),
            'owner' => $owner,
            'title' => 'Trip de test Assignment',
            'description' => 'Voyage utilisé pour tester la gestion des assignations.',
            'startDate' => new \DateTimeImmutable('+1 month'),
            'endDate' => new \DateTimeImmutable('+1 month +7 days'),
            'isDeleted' => false,
        ]);

//        Création d'une participation par utilisateur avec un status différent
        foreach ($usersByStatus as $status => $user) {
            ParticipationFactory::createOne([
                'createdAt' => new \DateTimeImmutable('2026-08-01 12:00:00'),
                'invitedBy' => $owner,
                'participant' => $user,
                'status' => ParticipationStatusEnum::from($status),
                'trip' => $trip,
            ]);
        }

//        Création d'un GroupItem avec une partie déjà assignée au owner
        $groupItemAssignedToOwner = GroupItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 13:00:00'),
            'name' => 'Bouteilles d\'eau',
            'totalQuantity' => 10,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::PIECE,
        ]);

//        Création d'un GroupItem avec une partie déjà assignée a un participant
        $groupItemAssignedToParticipant = GroupItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 13:00:00'),
            'name' => 'Chaises',
            'totalQuantity' => 10,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::PIECE,
        ]);


//        Création d'un GroupItem sans aucune assignations
        GroupItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 13:00:00'),
            'name' => 'Oranges',
            'totalQuantity' => 10,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::PIECE,
        ]);

//        Assignation des bouteilles d'eau au propriétaire du trip uniquement
        AssignmentFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 14:00:00'),
            'assignedQuantity' => 4,
            'isPacked' => false,
            'groupItem' => $groupItemAssignedToOwner,
            'assignedTo' => $owner,
        ]);

//        Création de deux assignations sur le groupItem Chaises
        AssignmentFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 14:00:00'),
            'assignedQuantity' => 4,
            'isPacked' => false,
            'groupItem' => $groupItemAssignedToParticipant,
            'assignedTo' => $usersByStatus[ParticipationStatusEnum::ACCEPTED->value ],
        ]);

        AssignmentFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 14:00:00'),
            'assignedQuantity' => 2,
            'isPacked' => false,
            'groupItem' => $groupItemAssignedToParticipant,
            'assignedTo' => $owner,
        ]);

//        Création d'un personal item pour le propriétaire
        PersonalItemFactory::createOne([
            'createdAt' => new \DateTimeImmutable('2026-08-01 15:00:00'),
            'name' => 'Brosse à dents',
            'quantity' => 1,
            'isPacked' => false,
            'owner' => $owner,
            'trip' => $trip,
            'unit' => GroupItemUnitEnum::PIECE,
        ]);
    }
}
