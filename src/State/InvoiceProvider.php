<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Invoice;
use App\Entity\Business;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class InvoiceProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();

        // admin sees all invoices
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->em->getRepository(Invoice::class)->findAll();
        }

        // fetch all businesses owned by user
        $businesses = $this->em->getRepository(Business::class)
                               ->findBy(['owner' => $user]);

        if (!$businesses) {
            return [];
        }

        $businessIds = array_map(fn($b) => $b->getId(), $businesses);

        return $this->em->getRepository(Invoice::class)
                        ->createQueryBuilder('i')
                        ->where('i.business IN (:ids)')
                        ->setParameter('ids', $businessIds)
                        ->getQuery()
                        ->getResult();
    }
}
