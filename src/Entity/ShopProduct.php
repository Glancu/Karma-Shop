<?php
declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use App\Traits\PriceTrait;
use App\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopProductRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="shop_products")
 * @ORM\Entity(repositoryClass=ShopProductRepository::class)
 */
class ShopProduct {
    use CreatedAtTrait, EnableTrait, UuidTrait, PriceTrait {
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
        EnableTrait::__construct as private __EnableTraitConstructor;
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
     * @Groups("shop_product")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var string
     *
     * @Groups("shop_product")
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-" )
     *
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @var integer
     *
     * @Groups("shop_product")
     *
     * @ORM\Column(type="smallint")
     */
    private int $quantity;

    /**
     * @var string
     *
     * @Groups("shop_product")
     *
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @var ShopBrand
     *
     * @Groups("shop_brand")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopBrand", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)
     */
    private ShopBrand $shopBrand;

    /**
     * @var ShopCategory
     *
     * @Groups("shop_category")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopCategory", inversedBy="products")
     * @ORM\JoinColumn(name="shop_category_id", referencedColumnName="id", nullable=false)
     */
    private ShopCategory $shopCategory;

    /**
     * @var ArrayCollection
     *
     * @Groups("shop_product_specification")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProductSpecification", cascade={"persist"})
     * @ORM\JoinTable(name="shop_products_product_specifications",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_specification_id", referencedColumnName="id")}
     * )
     */
    private $shopProductSpecifications;

    /**
     * @var ShopDelivery
     *
     * @Groups("shop_delivery")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopDelivery")
     * @ORM\JoinColumn(name="shop_delivery_id", referencedColumnName="id", nullable=false)
     */
    private ShopDelivery $shopDelivery;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\SonataMediaMedia")
     * @ORM\JoinTable(name="shop_products_images",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $images;

    /**
     * @var ArrayCollection
     *
     * @Groups("product_review")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductReview", inversedBy="products")
     * @ORM\JoinTable(name="shop_products_product_reviews")
     */
    private $reviews;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="products")
     */
    private $orders;

    /**
     * @var ShopColor
     *
     * @Groups("shop_color")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopColor", inversedBy="products")
     * @ORM\JoinTable(name="shop_products_colors")
     */
    private $shopColors;

    public function __construct()
    {
        $this->__CreatedAtTraitConstructor();
        $this->__EnableTraitConstructor();
        $this->__UuidTraitConstructor();
        $this->quantity = 1;
        $this->shopProductSpecifications = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->shopColors = new ArrayCollection();
    }

    /**
     * @return string
     */
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
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return null|ShopBrand
     */
    public function getShopBrand(): ?ShopBrand
    {
        return $this->shopBrand;
    }

    /**
     * @param ShopBrand $shopBrand
     */
    public function setShopBrand(ShopBrand $shopBrand): void
    {
        $this->shopBrand = $shopBrand;
    }

    /**
     * @return ShopCategory
     */
    public function getShopCategory(): ShopCategory
    {
        return $this->shopCategory;
    }

    /**
     * @param ShopCategory $shopCategory
     */
    public function setShopCategory(ShopCategory $shopCategory): void
    {
        $this->shopCategory = $shopCategory;
    }

    /**
     * @return ArrayCollection
     */
    public function getShopProductSpecifications()
    {
        return $this->shopProductSpecifications;
    }

    /**
     * @param ShopProductSpecification $shopProductSpecification
     */
    public function addShopProductSpecification(ShopProductSpecification $shopProductSpecification): void
    {
        $this->shopProductSpecifications[] = $shopProductSpecification;
    }

    /**
     * @param ShopProductSpecification $shopProductSpecification
     */
    public function removeShopProductSpecification(ShopProductSpecification $shopProductSpecification): void
    {
        $this->shopProductSpecifications->removeElement($shopProductSpecification);
    }

    /**
     * @return ShopDelivery
     */
    public function getShopDelivery(): ShopDelivery
    {
        return $this->shopDelivery;
    }

    /**
     * @param ShopDelivery $shopDelivery
     */
    public function setShopDelivery(ShopDelivery $shopDelivery): void
    {
        $this->shopDelivery = $shopDelivery;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param SonataMediaMedia $image
     */
    public function addImage(SonataMediaMedia $image): void
    {
        $this->images[] = $image;
    }

    /**
     * @param SonataMediaMedia $image
     */
    public function removeImage(SonataMediaMedia $image): void
    {
        $this->images->removeElement($image);
    }

    /**
     * @return ArrayCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param Comment $review
     */
    public function addReview(Comment $review): void
    {
        $this->reviews[] = $review;
    }

    /**
     * @param Comment $review
     */
    public function removeReview(Comment $review): void
    {
        $this->reviews->removeElement($review);
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getShopColors()
    {
        return $this->shopColors;
    }

    /**
     * @param ShopColor $color
     */
    public function addShopColor(ShopColor $color): void
    {
        $this->shopColors[] = $color;
    }

    /**
     * @param ShopColor $color
     */
    public function removeShopColor(ShopColor $color): void
    {
        $this->shopColors->removeElement($color);
    }
}
