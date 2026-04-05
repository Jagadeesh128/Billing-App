<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use App\State\InvoiceProvider;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['invoice:read']],
    denormalizationContext: ['groups' => ['invoice:write']],
    operations: [
        new GetCollection(
            provider: InvoiceProvider::class,
            security: "is_granted('ROLE_USER')",
        ),
        new Get(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
        ),
        new Post(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
        ),
        new Patch(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
        ),
        new Delete(
            security: "object.getBusiness().getOwner() == user or is_granted('ROLE_ADMIN')",
        ),
    ]
)]
#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Invoice
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['invoice:read'])]
    private ?string $invoiceNumber = null;

    #[ORM\Column]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $customerName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $customerPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $customerAddress = null;

    #[ORM\OneToMany(
        mappedBy: 'invoice',
        targetEntity: InvoiceItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Groups(['invoice:read', 'invoice:write'])]
    private Collection $items;

    #[ORM\Column(type: 'float')]
    #[Groups(['invoice:read'])]
    private ?float $subTotal = 0;

    #[ORM\Column(type: 'float')]
    #[Groups(['invoice:read'])]
    private ?float $taxTotal = 0;

    #[ORM\Column(type: 'float')]
    #[Groups(['invoice:read'])]
    private ?float $total = 0;

    #[ORM\Column(length: 255)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?Business $business = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->invoiceNumber ?? 'Invoice';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(?string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;
        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(?string $customerAddress): self
    {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    /**
     * @return Collection<int, InvoiceItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(InvoiceItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setInvoice($this);
        }
        return $this;
    }

    public function removeItem(InvoiceItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getInvoice() === $this) {
                $item->setInvoice(null);
            }
        }
        return $this;
    }

    public function getSubTotal(): ?float
    {
        return $this->subTotal;
    }

    public function setSubTotal(float $subTotal): self
    {
        $this->subTotal = $subTotal;
        return $this;
    }

    public function getTaxTotal(): ?float
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(float $taxTotal): self
    {
        $this->taxTotal = $taxTotal;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): self
    {
        $this->business = $business;
        return $this;
    }

    /**
     * Determine GST split: CGST/SGST vs IGST
     * Works only if Business entity has a `state` property.
     */
    public function isSameStateCustomer(): bool
    {
        $businessState = $this->getBusiness()?->getState();
        if (!$businessState) {
            return true;
        }

        $customerState = $this->extractStateFromAddress($this->getCustomerAddress());
        if (!$customerState) {
            return true;
        }

        return strtolower(trim($businessState)) === strtolower(trim($customerState));
    }

    private function extractStateFromAddress(?string $address): ?string
    {
        if (!$address) {
            return null;
        }

        $parts = array_map('trim', explode(',', $address));
        return end($parts) ?: null;
    }

    public function getInvoiceView(): self
    {
        return $this;
    }

}
