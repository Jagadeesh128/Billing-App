<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\State\BusinessProvider;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimestampableTrait;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\BusinessRepository;
use ApiPlatform\Metadata\GetCollection;
// use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['business:read']],
    denormalizationContext: ['groups' => ['business:write']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            provider: BusinessProvider::class,
            securityMessage: "You are not allowed."
        ),
        new Get(
            security: "object.getOwner() == user or is_granted('ROLE_ADMIN')",
            securityMessage: "You do not own this business."
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Only logged-in users can create businesses."
        ),
        new Patch(
            security: "object.getOwner() == user or is_granted('ROLE_ADMIN')",
            securityMessage: "You cannot edit this business."
        ),
        new Delete(
            security: "object.getOwner() == user or is_granted('ROLE_ADMIN')",
            securityMessage: "You cannot delete this business."
        ),
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: BusinessRepository::class)]
class Business
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['business:read', 'business:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['business:read', 'business:write'])]
    private ?bool $gstEnabled = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['business:read', 'business:write'])]
    private ?string $gstin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['business:read', 'business:write'])]
    private ?string $address = null;

    // #[ORM\Column]
    // #[Groups(['business:read'])]
    // private ?\DateTimeImmutable $createdAt = null;

    // #[ORM\Column]
    // #[Groups(['business:read'])]
    // private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'businesses')]
    #[Assert\NotNull(message: 'Owner is required')]
    #[Groups(['business:read'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'business', cascade: ['remove'])]
    #[Groups(['business:read'])]
    private Collection $invoices;

    #[ORM\OneToOne(mappedBy: 'business', targetEntity: BillingSettings::class, cascade: ['persist', 'remove'])]
    #[Groups(['business:read'])]
    private ?BillingSettings $billingSettings = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    // #[ORM\Column(length:50)]
    // private ?string $billingType = 'product'; // or service

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isGstEnabled(): ?bool
    {
        return $this->gstEnabled;
    }

    public function setGstEnabled(bool $gstEnabled): static
    {
        $this->gstEnabled = $gstEnabled;

        return $this;
    }

    public function getGstin(): ?string
    {
        return $this->gstin;
    }

    public function setGstin(string $gstin): static
    {
        $this->gstin = $gstin;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setBusiness($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getBusiness() === $this) {
                $invoice->setBusiness(null);
            }
        }

        return $this;
    }

    public function getBillingSettings(): ?BillingSettings
    {
        return $this->billingSettings;
    }

    public function setBillingSettings(BillingSettings $billingSettings): static
    {
        // set the owning side of the relation if necessary
        if ($billingSettings->getBusiness() !== $this) {
            $billingSettings->setBusiness($this);
        }

        $this->billingSettings = $billingSettings;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->owner?->getEmail() ?? 'No Owner', $this->gstin ?? 'No GSTIN');
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

}
