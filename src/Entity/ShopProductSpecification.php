<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ShopProductSpecificationRepository;
use App\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ShopProductSpecificationRepository::class)
 */
class ShopProductSpecification {
    use UuidTrait {
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
     * @var ShopProductSpecificationType|null
     *
     * @Groups("shop_product_specification_type")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopProductSpecificationType")
     * @ORM\JoinColumn(name="shop_product_specification_id", referencedColumnName="id", nullable=false)
     */
    private ?ShopProductSpecificationType $shopProductSpecificationType = null;

    /**
     * @var string
     *
     * @Groups("shop_product_specification")
     *
     * @ORM\Column(type="string")
     */
    private string $value = '';

    public function __construct(ShopProductSpecificationType $shopProductSpecificationType, string $value)
    {
        $this->__UuidTraitConstructor();
        $this->shopProductSpecificationType = $shopProductSpecificationType;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ShopProductSpecificationType|null
     */
    public function getShopProductSpecificationType(): ?ShopProductSpecificationType
    {
        return $this->shopProductSpecificationType;
    }

    /**
     * @param ShopProductSpecificationType $shopProductSpecificationType
     */
    public function setShopProductSpecificationType(ShopProductSpecificationType $shopProductSpecificationType): void
    {
        $this->shopProductSpecificationType = $shopProductSpecificationType;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
