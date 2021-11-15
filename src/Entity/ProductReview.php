<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\UuidTrait;
use App\Repository\ProductReviewRepository;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\EnableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductReviewRepository::class)
 */
class ProductReview
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
     * @Groups("product_review")
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
     * @Groups("product_review")
     *
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     *
     * @ORM\Column(type="string")
     */
    private string $email = '';

    /**
     * @var int
     *
     * @Groups("product_review")
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(5)
     *
     * @ORM\Column(type="smallint")
     */
    private int $rating = 0;

    /**
     * @var string
     *
     * @Groups("product_review")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="10")
     *
     * @ORM\Column(type="text")
     */
    private string $message = '';

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProduct", mappedBy="reviews")
     */
    private $products;

    public function __construct(
        string $name,
        string $email,
        int $rating,
        string $message,
        bool $enable = false
    ) {
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->email = $email;
        $this->rating = $rating;
        $this->message = $message;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
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
}
