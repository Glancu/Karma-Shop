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
}
