<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\Trip;
use App\Entity\User;
use App\Service\InviteParticipantService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class PostTripProcessor extends CustomProcessor
{
    public function __construct(
        Security                       $security,
        private EntityManagerInterface   $em,
        private InviteParticipantService $inviteParticipantService
    )
    {
        parent::__construct($security);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Trip
    {
        $currentUser = $this->getUser();

        $trip = new Trip();
        $trip->setOwner($currentUser)
            ->setTitle($data->title)
            ->setDescription($data->description)
            ->setStartDate($data->startDate)
            ->setEndDate($data->endDate)
            ->setCreatedAt(new \DateTimeImmutable());


        if (null !== $data->participants) {
            foreach ($data->participants as $participantDTO) {

                $participation = $this->inviteParticipantService->inviteParticipant($participantDTO->email, $currentUser, $trip);

                if (null !== $participation) {
                    $this->em->persist($participation);
                    $trip->addParticipation($participation);
                }
            }
        }

        $this->em->persist($trip);
        $this->em->flush();
        return $trip;
    }
}
