<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserTokenRepository $userTokenRepository,
        private MailerInterface $mailer
    )
    {

    }

    public function checkPreAuth(UserInterface $user): void
    {
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {

            if($existingValidToken = $this->userTokenRepository->findValidTokenByUserAndType($user, UserTokenTypeEnum::CHECK_EMAIL)){
                $existingValidToken->setExpiresAt(new \DateTimeImmutable());
            }

            $date = new \DateTimeImmutable();
            $rawToken = (bin2hex(random_bytes(32)));

            $userToken = new UserToken()
                ->setRequester($user)
                ->setType(UserTokenTypeEnum::CHECK_EMAIL)
                ->setCreatedAt($date)
                ->setExpiresAt($date->modify('+ 1 hour'))
                ->setToken(hash('sha256', $rawToken));

            $this->em->flush();

            $email = new Email()
                ->from('verification-mail@trouptrip.com')
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->html('<a href="https://trouptrip.com/api/check_email?">Check Email</a><p>' . $rawToken . '</p>');

            $this->mailer->send($email);

            throw new CustomUserMessageAccountStatusException(
                "Votre compte n'est pas encore vérifié. Un nouveau mail vient de vous être envoyé."
            );
        }
    }
}
