<?php

namespace App\State\Processor\Security;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\UserToken;
use App\Enum\UserTokenTypeEnum;
use App\Repository\UserRepository;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

readonly class ForgotPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private UserTokenRepository $userTokenRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->email]);

        if (!$user) {
            return;
        }

        if (!$user->isVerified()) {

            if ($existingValidToken = $this->userTokenRepository->findValidTokenByUserAndType($user, UserTokenTypeEnum::CHECK_EMAIL)) {
                $existingValidToken->setExpiresAt(new \DateTimeImmutable());
            }

            $date = new \DateTimeImmutable();
            $rawToken = bin2hex(random_bytes(32));

            $userToken = (new UserToken())
                ->setRequester($user)
                ->setType(UserTokenTypeEnum::CHECK_EMAIL)
                ->setCreatedAt($date)
                ->setExpiresAt($date->modify('+1 hour'))
                ->setToken(hash('sha256', $rawToken));

            $this->em->persist($userToken);
            $this->em->flush();

            // Envoi du mail de validation (Lien vers le Front-end)
            $email = (new Email())
                ->from('verification-mail@trouptrip.com')
                ->to($user->getEmail())
                ->subject('Activez votre compte TroupTrip')
                ->html('<p>Vous avez demandé à réinitialiser votre mot de passe, mais votre compte n\'est pas encore actif.</p>
                        <p>Veuillez d\'abord valider votre e-mail en cliquant ici :</p>
                        <a href="https://trouptrip.com/check-email?token=' . $rawToken . '">Activer mon compte</a><p>$rawToken</p>');

            $this->mailer->send($email);

            return;
        }

        $this->em->beginTransaction();

        try {
            if ($existingValidToken = $this->userTokenRepository->findValidTokenByUserAndType($user, UserTokenTypeEnum::RESET_PASSWORD)) {
                $existingValidToken->setExpiresAt(new \DateTimeImmutable());
            }

            $rawToken = bin2hex(random_bytes(32));
            $date = new \DateTimeImmutable();

            // Création du token de récupération
            $userToken = (new UserToken())
                ->setRequester($user)
                ->setToken(hash('sha256', $rawToken))
                ->setCreatedAt($date)
                ->setType(UserTokenTypeEnum::RESET_PASSWORD)
                ->setExpiresAt($date->modify('+15 min'));

            $this->em->persist($userToken);
            $this->em->flush();

            $email = (new Email())
                ->from('security@trouptrip.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->html('<p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous :</p>
                        <a href="https://trouptrip.com/reset-password?token=' . $rawToken . '">Réinitialiser mon mot de passe</a>');

            $this->mailer->send($email);

            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
