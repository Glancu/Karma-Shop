<?php
declare(strict_types=1);

namespace App\Entity;

use App\Traits\PriceTrait;
use App\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopDeliveryRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopDeliveryRepository::class)
 */
class ShopDelivery {
    use UuidTrait, PriceTrait {
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

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
    private string $name = '';

    /**
     * @var bool
     *
     * @Groups("shop_delivery")
     *
     * @ORM\COlumn(type="boolean")
     */
    private bool $freeDelivery = false;

    public function __construct(string $name, int $priceNet, int $priceGross, bool $freeDelivery = false) {
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->priceNet = $priceNet;
        $this->priceGross = $priceGross;
        $this->freeDelivery = $freeDelivery;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
}
