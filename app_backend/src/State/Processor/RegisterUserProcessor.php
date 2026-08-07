<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource\OutputRegisterUserDTO;
use App\Entity\User;
use App\Enum\EmailTypeEnum;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\UserTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private MailService $mailService,
        private UserTokenService $userTokenService
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): OutputRegisterUserDTO
    {
        if($existingUser = $this->userRepository->findOneBy(['email' => $data->email])) {
            $rawToken = $this->userTokenService->generateUserToken(UserTokenTypeEnum::RESET_PASSWORD, $existingUser);
            $this->mailService->sendSecurityEmail(EmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST, $existingUser, $rawToken);

            return new OutputRegisterUserDTO(
                $data->email,
                $data->firstname,
                $data->lastname
            );
        }

        $this->em->beginTransaction();

        try {
            $user = new User();
            $user->setFirstname($data->firstname)
                ->setLastname($data->lastname)
                ->setEmail($data->email)
                ->setPassword($this->passwordHasher->hashPassword($user, $data->plainPassword));

            $this->em->persist($user);
            $this->em->flush();

            $rawToken = $this->userTokenService->generateUserToken(UserTokenTypeEnum::CHECK_EMAIL, $user);
            $this->mailService->sendSecurityEmail(EmailTypeEnum::REGISTRATION_STANDARD, $user, $rawToken);

            $this->em->commit();

            return new OutputRegisterUserDTO(
                $user->getEmail(),
                $user->getFirstname(),
                $user->getLastname()
            );

        } catch (\Exception $e){
            $this->em->rollback();
            throw $e;
        }
    }
}
