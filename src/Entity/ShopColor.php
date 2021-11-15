<?php

namespace App\Entity;

use App\Repository\ShopColorRepository;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ShopColorRepository::class)
 */
class ShopColor
{
    use EnableTrait, CreatedAtTrait, UuidTrait {
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
     * @Groups("shop_color")
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     *
     * @var string
     *
     * @Groups("shop_color")
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-", unique=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProduct", mappedBy="shopColors")
     */
    private $products;

    public function __construct(string $name, bool $enable = true)
    {
        $this->__EnableTraitConstructor();
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->enable = $enable;
        $this->products = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

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

    public function addProduct(ShopProduct $product): void
    {
        $this->products[] = $product;
    }

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
