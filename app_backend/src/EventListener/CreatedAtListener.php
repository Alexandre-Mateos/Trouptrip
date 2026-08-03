<?php

namespace App\EventListener;

use App\Interface\CreatedAtInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist, priority: 500)]
final class CreatedAtListener
{
    public function prePersist(PrePersistEventArgs $event): void
    {
        $object = $event->getObject();
        if ($object instanceof CreatedAtInterface && null === $object->getCreatedAt()) {
            $object->setCreatedAt(new DateTimeImmutable());
        }
    }
}
