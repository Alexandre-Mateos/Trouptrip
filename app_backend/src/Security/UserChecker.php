<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserToken;
use App\Enum\EmailTypeEnum;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserTokenRepository;
use App\Service\MailService;
use App\Service\UserTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private MailService $mailService,
        private UserTokenService $userTokenService
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

            $rawToken = $this->userTokenService->generateUserToken(UserTokenTypeEnum::CHECK_EMAIL, $user);
            $this->mailService->sendSecurityEmail(EmailTypeEnum::LOGIN_UNVERIFIED_USER, $user, $rawToken);

            throw new CustomUserMessageAccountStatusException(
                "Votre compte n'est pas encore vérifié. Un nouveau mail vient de vous être envoyé."
            );
        }
    }
}
