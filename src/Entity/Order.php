<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\PriceTrait;
use App\Traits\UuidTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    use CreatedAtTrait, UuidTrait, PriceTrait {
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $methodPay; // @TODO Create function with const

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $blockPay;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status; // @TODO Create function with const

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $discount;

    /**
     * @ORM\Column(name="additional_information", type="text", nullable=true)
     */
    private ?string $additionalInformation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProduct")
     */
    private ArrayCollection $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopDelivery")
     * @ORM\JoinColumn(nullable=false)
     */
    private ShopDelivery $delivery;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClientUser", inversedBy="orders")
     * @ORM\JoinTable(name="orders_client_users")
     */
    private $user;

    public function __construct()
    {
        $this->__UuidTraitConstructor();
        $this->blockPay = false;
        $this->products = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethodPay(): ?int
    {
        return $this->methodPay;
    }

    public function setMethodPay(int $methodPay): self
    {
        $this->methodPay = $methodPay;

        return $this;
    }

    public function isBlockPay(): bool
    {
        return $this->blockPay;
    }

    public function setBlockPay(bool $blockPay): void
    {
        $this->blockPay = $blockPay;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(?string $additionalInformation): self
    {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct(ShopProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(ShopProduct $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    public function getDelivery(): ?ShopDelivery
    {
        return $this->delivery;
    }

    public function setDelivery(ShopDelivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function addUser(ClientUser $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(ClientUser $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }
}
