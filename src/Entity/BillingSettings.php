<?php

namespace App\Entity;

use App\Repository\BillingSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['billing:read']],
    denormalizationContext: ['groups' => ['billing:write']],
    operations: [
        new Get(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
            securityMessage: "You do not own this Business."
        ),
        new Patch(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
            securityMessage: "You cannot edit these billing settings."
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Only authenticated users can create settings."
        ),
    ]
)]
#[ORM\Entity(repositoryClass: BillingSettingsRepository::class)]
class BillingSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['billing:read', 'billing:write'])]
    private ?string $invoicePrefix = null;

    #[ORM\Column]
    #[Groups(['billing:read', 'billing:write'])]
    private ?int $invoiceStartNumber = null;

    #[ORM\Column]
    #[Groups(['billing:read', 'billing:write'])]
    private ?float $defaultTaxRate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['billing:read', 'billing:write'])]
    private ?string $terms = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['billing:read', 'billing:write'])]
    private ?string $logoUrl = null;

    #[ORM\OneToOne(inversedBy: 'billingSettings', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['billing:read', 'billing:write'])]
    private ?Business $business = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoicePrefix(): ?string
    {
        return $this->invoicePrefix;
    }

    public function setInvoicePrefix(string $invoicePrefix): static
    {
        $this->invoicePrefix = $invoicePrefix;

        return $this;
    }

    public function getInvoiceStartNumber(): ?int
    {
        return $this->invoiceStartNumber;
    }

    public function setInvoiceStartNumber(int $invoiceStartNumber): static
    {
        $this->invoiceStartNumber = $invoiceStartNumber;

        return $this;
    }

    public function getDefaultTaxRate(): ?float
    {
        return $this->defaultTaxRate;
    }

    public function setDefaultTaxRate(float $defaultTaxRate): static
    {
        $this->defaultTaxRate = $defaultTaxRate;

        return $this;
    }

    public function getTerms(): ?string
    {
        return $this->terms;
    }

    public function setTerms(?string $terms): static
    {
        $this->terms = $terms;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): static
    {
        $this->business = $business;

        return $this;
    }
}
