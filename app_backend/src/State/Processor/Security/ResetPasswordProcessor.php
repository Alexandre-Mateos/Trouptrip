<?php

namespace App\State\Processor\Security;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\UserTokenTypeEnum;
use App\Exception\TokenExpiredException;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class ResetPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): void {
        $userToken = $this->userTokenRepository->findValidTokenByTypeAndEmail(
            hash('sha256', $data->token),
            UserTokenTypeEnum::RESET_PASSWORD,
            $data->email
        );

        if (!$userToken) {
            throw new TokenExpiredException();
        }

        $user = $userToken->getRequester();

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data->plainPassword)
        );

        $userToken->setExpiresAt(new \DateTimeImmutable());

        $this->em->flush();
    }
}
