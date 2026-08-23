<?php

namespace App\Tests\trip;

use App\Entity\Trip;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Tests\AbstractApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractTripApiTestCase extends AbstractApiTestCase
{
    protected static string $TRIPS = '/api/trips';

    protected TripRepository $tripRepository;
    protected UserRepository $userRepository;
    protected Security $security;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultUrl = self::$TRIPS;

        $this->tripRepository = $this->getEntity(TripRepository::class);
        $this->userRepository = $this->getEntity(UserRepository::class);
        $this->security = $this->getEntity(Security::class);
        $this->entityManager = $this->getEntity(EntityManagerInterface::class);
    }

    protected function getTestTrip(string $title): Trip
    {
        return $this->tripRepository->findOneBy(['title' => $title]);
    }

    protected function refreshTestTrip(int $tripId): Trip
    {
        $this->entityManager->clear();
        return $this->tripRepository->find($tripId);
    }
}
