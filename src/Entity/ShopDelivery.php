<?php
declare(strict_types=1);

namespace App\Entity;

use App\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopDeliveryRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopDeliveryRepository::class)
 */
class ShopDelivery {
    use UuidTrait {
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     *
     * @Groups("shop_delivery")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var bool
     *
     * @Groups("shop_delivery")
     *
     * @ORM\COlumn(type="boolean")
     */
    private bool $freeDelivery;

    /**
     * @var null|float
     *
     * @Groups("shop_delivery")
     *
     * @ORM\Column(type="float", scale=2, precision=10, nullable=true)
     */
    private ?float $priceNet;

    /**
     * @var null|float
     *
     * @Groups("shop_delivery")
     *
     * @ORM\Column(type="float", scale=2, precision=10, nullable=true)
     */
    private ?float $priceGross;

    public function __construct() {
        $this->__UuidTraitConstructor();
        $this->freeDelivery = false;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isFreeDelivery(): bool
    {
        return $this->freeDelivery;
    }

    /**
     * @param bool $freeDelivery
     */
    public function setFreeDelivery(bool $freeDelivery): void
    {
        $this->freeDelivery = $freeDelivery;
    }

    /**
     * @return float
     */
    public function getPriceNet(): ?float
    {
        return $this->priceNet;
    }

    /**
     * @param float|null $priceNet
     */
    public function setPriceNet(?float $priceNet): void
    {
        $this->priceNet = $priceNet;
    }

    /**
     * @return float
     */
    public function getPriceGross(): ?float
    {
        return $this->priceGross;
    }

    /**
     * @param float|null $priceGross
     */
    public function setPriceGross(?float $priceGross): void
    {
        $this->priceGross = $priceGross;
    }
}
