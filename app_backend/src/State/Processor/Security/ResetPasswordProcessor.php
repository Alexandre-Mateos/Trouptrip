<?php

namespace App\State\Processor\Security;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\UserTokenTypeEnum;
use App\Exception\TokenExpiredException;
use App\Exception\VerifiedUserException;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class ResetPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $hashedToken = hash('sha256', $data->token);
        $userToken = $this->userTokenRepository->findValidTokenByType($hashedToken, UserTokenTypeEnum::RESET_PASSWORD);

        if (!$userToken) {
            throw new TokenExpiredException("Le jeton de récupération est invalide ou a expiré.");
        }

        $user = $userToken->getRequester();

        $this->em->beginTransaction();

        try {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data->plainPassword);
            $user->setPassword($hashedPassword);

            $this->em->remove($userToken);

            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
