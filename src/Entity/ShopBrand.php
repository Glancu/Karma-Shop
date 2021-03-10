<?php

namespace App\Entity;

use App\Repository\ShopBrandRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use App\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ShopBrandRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ShopBrand
{
    use EnableTrait, CreatedAtTrait, UuidTrait {
        EnableTrait::__construct as private __EnableTraitConstructor;
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @Groups("shop_brand")
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private string $title;

    /**
     * @Groups("shop_brand")
     *
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShopProduct", mappedBy="shopBrand")
     */
    private $products;

    /**
     * ShopBrand constructor.
     */
    public function __construct()
    {
        $this->__EnableTraitConstructor();
        $this->__CreatedAtTraitConstructor();
        $this->__UuidTraitConstructor();
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
     * @return null|ArrayCollection
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
