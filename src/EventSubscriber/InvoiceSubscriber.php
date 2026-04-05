<?php

namespace App\EventSubscriber;

use App\Entity\Invoice;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;

class InvoiceSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
        ];
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        /** ----------------------------------------------------
         * 1️⃣ HANDLE NEW INVOICES (INSERTIONS)
         * ---------------------------------------------------- */
        foreach ($uow->getScheduledEntityInsertions() as $entity) {

            if (!$entity instanceof Invoice) {
                continue;
            }

            $this->generateInvoiceNumber($entity);
            $this->calculateTotals($entity);

            // recompute invoice changeset
            $meta = $em->getClassMetadata(Invoice::class);
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        }

        /** ----------------------------------------------------
         * 2️⃣ HANDLE UPDATED INVOICES (ITEMS EDITED)
         * ---------------------------------------------------- */
        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if (!$entity instanceof Invoice) {
                continue;
            }

            $this->calculateTotals($entity);

            $meta = $em->getClassMetadata(Invoice::class);
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }

    private function generateInvoiceNumber(Invoice $invoice): void
    {
        if ($invoice->getInvoiceNumber()) {
            return; // already generated
        }

        $business = $invoice->getBusiness();
        $settings = $business?->getBillingSettings();

        if (!$settings) {
            throw new \RuntimeException("Billing Settings missing for business: " . $business->getName());
        }

        $prefix = $settings->getInvoicePrefix() ?? 'INV';
        $number = $settings->getInvoiceStartNumber() ?? 1;

        $invoiceNumber = sprintf('%s-%04d', $prefix, $number);
        $invoice->setInvoiceNumber($invoiceNumber);

        // increment counter
        $settings->setInvoiceStartNumber($number + 1);
    }

    private function calculateTotals(Invoice $invoice): void
    {
        $sub = 0;
        $tax = 0;

        foreach ($invoice->getItems() as $item) {
            $qty = $item->getQuantity() ?? 1;
            $price = $item->getUnitPrice() ?? 0;

            $line = $qty * $price;
            $item->setLineTotal($line);

            $sub += $line;

            $rate = $item->getTaxRate() ?? 0;
            $tax += ($line * $rate) / 100;
        }

        $invoice->setSubTotal($sub);
        $invoice->setTaxTotal($tax);
        $invoice->setTotal($sub + $tax);
    }
}
