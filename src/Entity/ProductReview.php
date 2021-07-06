<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductReviewRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductReviewRepository::class)
 */
class ProductReview
{
    use EnableTrait, CreatedAtTrait {
        EnableTrait::__construct as private __EnableTraitConstructor;
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
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
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="string")
     */
    private string $name = '';

    /**
     * @var string
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
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(5)
     *
     * @ORM\Column(type="smallint")
     */
    private int $rating = 0;

    /**
     * @var null|string
     *
     * @Assert\Length(min="9")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $phoneNumber = null;

    /**
     * @var string
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
        bool $enable = false,
        ?string $phoneNumber = null
    ) {
        $this->__CreatedAtTraitConstructor();
        $this->name = $name;
        $this->email = $email;
        $this->rating = $rating;
        $this->message = $message;
        $this->enable = $enable;
        $this->phoneNumber = $phoneNumber;
        $this->products = new ArrayCollection();
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
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
}
