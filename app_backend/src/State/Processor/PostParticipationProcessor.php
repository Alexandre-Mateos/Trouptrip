<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Participation;
use App\Entity\Trip;
use App\Enum\ParticipationStatusEnum;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PostParticipationProcessor extends CustomProcessor
{
    public function __construct(
        Security                   $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private IriConverterInterface $iriConverter,
        private UserRepository $userRepository,
    )
    {
        parent::__construct($security);
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Participation
    {
        /** @var Trip $trip */
        $trip = $this->iriConverter->getResourceFromIri($data->trip);
        $currentUser = $this->getUser();
        $invitedUser = $this->userRepository->findOneBy(['email' => $data->email]);
        $participation = new Participation();
        $participation->setParticipant($invitedUser)
            ->setTrip($trip)
            ->setInvitedBy($currentUser)
            ->setStatus(ParticipationStatusEnum::PENDING);

        return $this->persistProcessor->process($participation, $operation, $uriVariables, $context);
    }
}
