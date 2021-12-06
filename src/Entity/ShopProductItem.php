<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PriceTrait;
use App\Repository\ShopProductItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ORM\Entity(repositoryClass=ShopProductItemRepository::class)
 * @ORM\Table(name="shop_product_items")
 */
final class ShopProductItem
{
    use PriceTrait;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var ShopProduct
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopProduct")
     * @ORM\JoinColumn(name="shop_product_id", referencedColumnName="id", nullable=false)
     */
    private ShopProduct $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private int $quantity;

    public function __construct(ShopProduct $shopProduct, int $quantity)
    {
        $this->product = $shopProduct;
        $this->quantity = $quantity;

        $this->priceNet = $shopProduct->getPriceNet();
        $this->priceGross = $shopProduct->getPriceGross();
    }

    public function __toString(): string
    {
        return $this->product->getName();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ShopProduct
     */
    public function getProduct(): ShopProduct
    {
        return $this->product;
    }

    /**
     * @param ShopProduct $product
     */
    public function setProduct(ShopProduct $product): void
    {
        $this->product = $product;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
