<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\PriceTrait;
use App\Entity\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShopProductRepository;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ORM\Table(name="shop_products")
 * @ORM\Entity(repositoryClass=ShopProductRepository::class)
 */
class ShopProduct
{
    use CreatedAtTrait, EnableTrait, UuidTrait, PriceTrait {
        EnableTrait::__construct as private __EnableTraitConstructor;
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
     * @Groups("shop_product")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="string")
     */
    private string $name = '';

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
     * @Assert\Positive
     *
     * @Groups("shop_product")
     *
     * @ORM\Column(type="smallint")
     */
    private int $quantity = 1;

    /**
     * @var string
     *
     * @Groups("shop_product")
     *
     * @ORM\Column(type="text")
     */
    private string $description = '';

    /**
     * @var ShopBrand|null
     *
     * @Groups("shop_brand")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopBrand", inversedBy="products")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)
     */
    private ?ShopBrand $shopBrand = null;

    /**
     * @var ShopCategory|null
     *
     * @Groups("shop_category")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopCategory", inversedBy="products")
     * @ORM\JoinColumn(name="shop_category_id", referencedColumnName="id", nullable=false)
     */
    private ?ShopCategory $shopCategory = null;

    /**
     * @var ArrayCollection|PersistentCollection
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
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\SonataMediaMedia")
     * @ORM\JoinTable(name="shop_products_images",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $images;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @Groups("product_review")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductReview", inversedBy="products")
     * @ORM\JoinTable(name="shop_products_product_reviews")
     */
    private $reviews;

    /**
     * @var ShopColor
     *
     * @Groups("shop_color")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopColor", inversedBy="products")
     * @ORM\JoinTable(name="shop_products_colors")
     */
    private $shopColors;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @Groups("comment")
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment")
     * @ORM\JoinTable(name="shop_products_comments",
     *      joinColumns={@ORM\JoinColumn(name="shop_product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $comments;

    public function __construct(
        string $name,
        int $priceNet,
        int $priceGross,
        int $quantity,
        string $description,
        ShopBrand $shopBrand,
        ShopCategory $shopCategory,
        $shopProductSpecifications,
        $images,
        bool $enable = true,
        $colors = []
    ) {
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->priceNet = $priceNet;
        $this->priceGross = $priceGross;
        $this->quantity = $quantity;
        $this->description = $description;
        $this->shopBrand = $shopBrand;
        $this->shopCategory = $shopCategory;
        $this->shopProductSpecifications = $shopProductSpecifications;
        $this->enable = $enable;
        $this->images = $images;
        $this->reviews = new ArrayCollection();
        $this->shopColors = $colors;
        $this->comments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getShopBrand(): ?ShopBrand
    {
        return $this->shopBrand;
    }

    public function setShopBrand(ShopBrand $shopBrand): void
    {
        $this->shopBrand = $shopBrand;
    }

    public function getShopCategory(): ?ShopCategory
    {
        return $this->shopCategory;
    }

    public function setShopCategory(ShopCategory $shopCategory): void
    {
        $this->shopCategory = $shopCategory;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getShopProductSpecifications()
    {
        return $this->shopProductSpecifications;
    }

    public function addShopProductSpecification(ShopProductSpecification $shopProductSpecification): void
    {
        $this->shopProductSpecifications[] = $shopProductSpecification;
    }

    public function removeShopProductSpecification(ShopProductSpecification $shopProductSpecification): void
    {
        $this->shopProductSpecifications->removeElement($shopProductSpecification);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function addImage(SonataMediaMedia $image): void
    {
        $this->images[] = $image;
    }

    public function removeImage(SonataMediaMedia $image): void
    {
        $this->images->removeElement($image);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    public function addReview(ProductReview $review): void
    {
        $this->reviews[] = $review;
    }

    public function removeReview(ProductReview $review): void
    {
        $this->reviews->removeElement($review);
    }

    public function getShopColors()
    {
        return $this->shopColors;
    }

    public function addShopColor(ShopColor $color): void
    {
        $this->shopColors[] = $color;
    }

    public function removeShopColor(ShopColor $color): void
    {
        $this->shopColors->removeElement($color);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }
}
