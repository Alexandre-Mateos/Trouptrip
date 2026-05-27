<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\SecurityEmailTypeEnum;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class MailService
{
    const string EMAIL_FROM = 'security@trouptrip.fr';
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public function sendEmail(SecurityEmailTypeEnum $type, User $user, string $rawToken): void
    {
        $baseUrl = $this->parameterBag->get('frontend_url');
        $url = $baseUrl . $this->getAssociatedFrontendBaseUrl($type) . $rawToken;

        $email = new TemplatedEmail()
            ->from(self::EMAIL_FROM)
            ->to($user->getEmail())
            ->subject($this->getSubject($type))
            ->htmlTemplate($this->getAssociatedMailTemplate($type))
            ->context([
                'firstname' => $user->getFirstname(),
                'url' => $url
            ]);

        $this->mailer->send($email);
    }

    private function getSubject(SecurityEmailTypeEnum $type): string
    {
        return match($type){
            SecurityEmailTypeEnum::REGISTRATION_STANDARD => 'Bienvenue sur Trouptrip',
            SecurityEmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => 'Bon retour parmi nous',
            SecurityEmailTypeEnum::LOGIN_UNVERIFIED_USER, SecurityEmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER => 'Vous devez d\'abord activer votre compte',
            SecurityEmailTypeEnum::RESET_PASSWORD_STANDARD => 'Vous avez fais une demande de réinitialisation de mot de passe',
        };
    }

    private function getAssociatedMailTemplate(SecurityEmailTypeEnum $type): string
    {
        return match($type){
            SecurityEmailTypeEnum::REGISTRATION_STANDARD => 'email/security/registration_standard.html.twig',
            SecurityEmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => 'email/security/registration_user_already_exist.html.twig',
            SecurityEmailTypeEnum::LOGIN_UNVERIFIED_USER => 'email/security/login_unverified_user.html.twig',
            SecurityEmailTypeEnum::RESET_PASSWORD_STANDARD => 'email/security/reset_password_standard.html.twig',
            SecurityEmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER => 'email/security/reset_password_unverified_user.html.twig'
        };
    }

    private function getAssociatedFrontendBaseUrl(SecurityEmailTypeEnum $type): string
    {
        return match($type){
            SecurityEmailTypeEnum::REGISTRATION_STANDARD, SecurityEmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER, SecurityEmailTypeEnum::LOGIN_UNVERIFIED_USER  => '/verify-email?token=',
            SecurityEmailTypeEnum::RESET_PASSWORD_STANDARD, SecurityEmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => '/reset-password?token='
        };
    }
}
