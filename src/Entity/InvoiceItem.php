<?php

namespace App\Entity;

use App\Repository\InvoiceItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: InvoiceItemRepository::class)]
class InvoiceItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['invoice:read','invoice:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Groups(['invoice:read','invoice:write'])]
    private ?string $type = null;

    #[ORM\Column(type: "float")]
    #[Groups(['invoice:read','invoice:write'])]
    private ?float $unitPrice = null;

    #[ORM\Column(type: "float")]
    #[Groups(['invoice:read','invoice:write'])]
    private ?float $taxRate = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $cgst = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $sgst = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $igst = null;

    #[ORM\Column(type: "float")]
    #[Groups(['invoice:read'])]
    private ?float $lineTotal = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['invoice:read','invoice:write'])]
    private ?string $hsnSac = null;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity = 1;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateLineTotal(): void
    {
        $qty = $this->quantity ?? 1;
        $price = $this->unitPrice ?? 0;
        $this->lineTotal = $qty * $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(float $taxRate): static
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getCgst(): ?float
    {
        return $this->cgst;
    }

    public function setCgst(?float $cgst): static
    {
        $this->cgst = $cgst;

        return $this;
    }

    public function getSgst(): ?float
    {
        return $this->sgst;
    }

    public function setSgst(?float $sgst): static
    {
        $this->sgst = $sgst;

        return $this;
    }

    public function getIgst(): ?float
    {
        return $this->igst;
    }

    public function setIgst(?float $igst): static
    {
        $this->igst = $igst;

        return $this;
    }

    public function getLineTotal(): ?float
    {
        return $this->lineTotal;
    }

    public function setLineTotal(float $lineTotal): static
    {
        $this->lineTotal = $lineTotal;

        return $this;
    }

    public function getHsnSac(): ?string
    {
        return $this->hsnSac;
    }

    public function setHsnSac(?string $hsnSac): static
    {
        $this->hsnSac = $hsnSac;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
