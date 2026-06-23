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

        $startDate = new \DateTimeImmutable($data->startDate);
        $endDate = new \DateTimeImmutable($data->endDate);

        $trip = new Trip();
        $trip->setOwner($currentUser)
            ->setTitle($data->title)
            ->setDescription($data->description)
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($trip);
        $this->em->flush();
        return $trip;
    }
}
