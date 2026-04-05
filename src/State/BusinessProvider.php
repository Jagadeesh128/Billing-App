<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Business;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BusinessProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();

        // admin sees everything
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->em->getRepository(Business::class)->findAll();
        }

        // normal user: return only his businesses
        return $this->em->getRepository(Business::class)
                        ->findBy(['owner' => $user]);
    }
}
