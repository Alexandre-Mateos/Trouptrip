<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\UserTokenTypeEnum;
use App\Exception\TokenExpiredException;
use App\Repository\UserTokenRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

readonly class VerifyEmailProcessor implements ProcessorInterface
{
    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private EntityManagerInterface $em
    )
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $userToken = $this->userTokenRepository->findValidTokenByType(hash('sha256', $data->token), UserTokenTypeEnum::CHECK_EMAIL);
        if(!$userToken){
            throw new TokenExpiredException();
        }

        $user = $userToken->getRequester();
        $this->em->beginTransaction();

        try{

            $user->setIsVerified(true);
            $this->em->persist($user);

            $userToken->setExpiresAt(new \DateTimeImmutable());
            $this->em->persist($userToken);

            $this->em->flush();
            $this->em->commit();

        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            throw new ConflictHttpException("Cette ressource existe déjà.");
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new \RuntimeException("Erreur technique imprévue.");
        }
    }
}
