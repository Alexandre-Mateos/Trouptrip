<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function onLogout(LogoutEvent $event): void
    {
        if (str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            $event->setResponse(new JsonResponse([
                'message' => 'Déconnexion réussie'
            ], 200));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
