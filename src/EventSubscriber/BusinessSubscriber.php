<?php

namespace App\EventSubscriber;

use App\Entity\BillingSettings;
use App\Entity\Business;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BusinessSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Business) {
            return;
        }

        $em = $args->getObjectManager();

        // Auto-create billing settings
        $billingSettings = new BillingSettings();
        $billingSettings->setBusiness($entity);
        $billingSettings->setInvoicePrefix('INV');
        $billingSettings->setInvoiceStartNumber(1);
        $billingSettings->setDefaultTaxRate(18);
        $billingSettings->setTerms('');
        $billingSettings->setLogoUrl(null);

        // Link back
        $entity->setBillingSettings($billingSettings);

        $em->persist($billingSettings);
        $em->flush();
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Business) {
            return;
        }

        if (!$entity->getBillingSettings()) {
            $settings = new BillingSettings();
            $settings->setBusiness($entity);
            $settings->setInvoicePrefix('INV');
            $settings->setInvoiceStartNumber(1);
            $settings->setDefaultTaxRate(18);

            $entity->setBillingSettings($settings);
        }
    }

}
