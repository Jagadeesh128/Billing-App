<?php

namespace App\EventSubscriber;

use App\Entity\Business;
use App\Entity\BillingSettings;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BusinessBillingSettingsSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [Events::postPersist];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Business) {
            return;
        }

        $em = $args->getObjectManager();

        // Create default billing settings
        $settings = new BillingSettings();
        $settings->setInvoicePrefix('INV');
        $settings->setInvoiceStartNumber(1);
        $settings->setDefaultTaxRate(18);
        $settings->setBusiness($entity);

        $em->persist($settings);
        $em->flush();
    }
}
