<?php

namespace App\Entity;

use App\Repository\ShopCategoryRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use App\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ShopCategoryRepository::class)
 */
class ShopCategory
{
    use EnableTrait, CreatedAtTrait, UuidTrait {
        EnableTrait::__construct as private __EnableTraitConstructor;
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
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
     * @Groups("shop_category")
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $title = '';

    /**
     * @Groups("shop_category")
     *
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ShopProduct", mappedBy="shopCategory")
     */
    private $products;

    /**
     * ShopCategory constructor.
     */
    public function __construct(string $title, bool $enable = true)
    {
        $this->__EnableTraitConstructor();
        $this->__CreatedAtTraitConstructor();
        $this->__UuidTraitConstructor();
        $this->title = $title;
        $this->enable = $enable;
        $this->products = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * @return ArrayCollection|PersistentCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ShopProduct $product
     */
    public function addProduct(ShopProduct $product): void
    {
        $this->products[] = $product;
    }

    /**
     * @param ShopProduct $product
     */
    public function removeProduct(ShopProduct $product): void
    {
        $this->products->removeElement($product);
    }

    /**
     * CUSTOM
     */

    public function getCountProducts(): int
    {
        $count = 0;

        /**
         * @var ShopProduct $product
         */
        foreach($this->products as $product) {
            if($product->isEnable()) {
                $count++;
            }
        }

        return $count;
    }
}
