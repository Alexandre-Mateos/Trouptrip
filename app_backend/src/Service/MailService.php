<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\EmailTypeEnum;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class MailService
{
    const string SECURITY_EMAIL_FROM = 'security@trouptrip.fr';
    const string NOTIFICATION_EMAIL_FROM = 'info@trouptrip.fr';
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public function sendSecurityEmail(EmailTypeEnum $type, User $user, string $rawToken): void
    {
        $baseUrl = $this->parameterBag->get('frontend_url');
        $url = $baseUrl . $this->getAssociatedFrontendBaseUrl($type);

        $email = new TemplatedEmail()
            ->from(self::SECURITY_EMAIL_FROM)
            ->to($user->getEmail())
            ->subject($this->getSubject($type))
            ->htmlTemplate($this->getAssociatedMailTemplate($type))
            ->context([
                'firstname' => $user->getFirstname(),
                'url' => $url,
                'rawToken' => $rawToken,
                'buttonLabel' => $this->getActionButtonLabel($type)
            ]);

        $this->mailer->send($email);
    }

    public function sendInvitationEmail(EmailTypeEnum $type, User $invitedUser, Trip $trip, User $invitedBy): void
    {
        $baseUrl = $this->parameterBag->get('frontend_url');
        $email = new TemplatedEmail()
            ->from(self::NOTIFICATION_EMAIL_FROM)
            ->to($invitedUser->getEmail())
            ->subject($this->getSubject($type))
            ->htmlTemplate($this->getAssociatedMailTemplate($type))
            ->context([
                'invitedUser' => $invitedUser,
                'trip' => $trip,
                'invitedBy' => $invitedBy,
                'url' => $baseUrl
            ]);

        $this->mailer->send($email);
    }

    private function getSubject(EmailTypeEnum $type): string
    {
        return match($type){
            EmailTypeEnum::REGISTRATION_STANDARD => 'Bienvenue sur Trouptrip',
            EmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => 'Bon retour parmi nous',
            EmailTypeEnum::LOGIN_UNVERIFIED_USER, EmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER => 'Vous devez d\'abord activer votre compte',
            EmailTypeEnum::RESET_PASSWORD_STANDARD => 'Vous avez fais une demande de réinitialisation de mot de passe',
            EmailTypeEnum::INVITE_USER_TO_TRIP => 'Vous avez reçu une nouvelle invitation',
        };
    }

    private function getAssociatedMailTemplate(EmailTypeEnum $type): string
    {
        return match($type){
            EmailTypeEnum::REGISTRATION_STANDARD => 'email/security/registration_standard.html.twig',
            EmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => 'email/security/registration_user_already_exist.html.twig',
            EmailTypeEnum::LOGIN_UNVERIFIED_USER => 'email/security/login_unverified_user.html.twig',
            EmailTypeEnum::RESET_PASSWORD_STANDARD => 'email/security/reset_password_standard.html.twig',
            EmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER => 'email/security/reset_password_unverified_user.html.twig',
            EmailTypeEnum::INVITE_USER_TO_TRIP => 'email/info/invitation_user.html.twig',
        };
    }

    private function getAssociatedFrontendBaseUrl(EmailTypeEnum $type): string
    {
        return match($type){
            EmailTypeEnum::REGISTRATION_STANDARD, EmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER, EmailTypeEnum::LOGIN_UNVERIFIED_USER  => '/verify-email',
            EmailTypeEnum::RESET_PASSWORD_STANDARD, EmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => '/reset-password'
        };
    }

    private function getActionButtonLabel(EmailTypeEnum $type): string
    {
        return match($type){
            EmailTypeEnum::REGISTRATION_STANDARD, EmailTypeEnum::RESET_PASSWORD_UNVERIFIED_USER, EmailTypeEnum::LOGIN_UNVERIFIED_USER  => 'Vérifier mon email',
            EmailTypeEnum::RESET_PASSWORD_STANDARD, EmailTypeEnum::REGISTRATION_USER_ALREADY_EXIST => 'Réinitialiser mon mot de passe'
        };
    }
}
