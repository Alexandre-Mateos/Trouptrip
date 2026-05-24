<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserResource\OutputUserDTO;
use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
        private UserRepository $userRepository
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): OutputUserDTO
    {
        if($this->userRepository->findIsExistingEmail($data->email)) {
            $email = new Email()
                ->from('verification-mail@trouptrip.com')
                ->to($data->email)
                ->subject('Time for Symfony Mailer!')
                ->html('<p> Un compte existe déjà pour cette adresse email. Nous vous invitons à réinitialiser votre mot de passe </p>');

            $this->mailer->send($email);

            return new OutputUserDTO(
                $data->email,
                $data->firstname,
                $data->lastname
            );
        }


        try {
            $user = new User();
            $user->setFirstname($data->firstname)
                ->setLastname($data->lastname)
                ->setEmail($data->email)
                ->setPassword($this->passwordHasher->hashPassword($user, $data->plainPassword));

            $this->em->beginTransaction();

            $this->em->persist($user);

            $rawToken = bin2hex(random_bytes(32));
            $date = new \DateTimeImmutable();

            $userToken = new UserToken()
                ->setRequester($user)
                ->setToken(hash('sha256', $rawToken))
                ->setCreatedAt($date)
                ->setType(UserTokenTypeEnum::CHECK_EMAIL)
                ->setExpiresAt($date->modify('+ 1 hour'));

            $this->em->persist($userToken);
            $this->em->flush();

            $email = new Email()
                ->from('verification-mail@trouptrip.com')
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->html('<a href="https://trouptrip.com/api/check_email?">Check Email</a><p>' . $rawToken . '</p>');

            $this->mailer->send($email);

            $this->em->commit();

            return new OutputUserDTO(
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
