<?php

namespace App\State\Processor\Security;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\SecurityEmailTypeEnum;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\UserTokenService;
use Doctrine\ORM\EntityManagerInterface;

readonly class ForgotPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private UserTokenService $userTokenService,
        private MailService $mailService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->email]);

        if (!$user) {
            return;
        }

        if (!$user->isVerified()) {

            $rawToken = $this->userTokenService->generateUserToken(UserTokenTypeEnum::CHECK_EMAIL, $user);
            $this->mailService->sendEmail(SecurityEmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER, $user, $rawToken);

            return;
        }

        $this->em->beginTransaction();

        try {
            $rawToken = $this->userTokenService->generateUserToken(UserTokenTypeEnum::RESET_PASSWORD,$user);
            $this->mailService->sendEmail(SecurityEmailTypeEnum::RESET_PASSWORD_STANDARD, $user, $rawToken);

            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
