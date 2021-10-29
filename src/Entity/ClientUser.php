<?php

namespace App\Entity;

use App\Repository\ClientUserRepository;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClientUserRepository::class)
 */
class ClientUser implements UserInterface
{
    use CreatedAtTrait, UuidTrait {
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = 0;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $email = '';

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The password cannot be empty."
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    public string $password = '';

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    private $orders;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="password_changed_at", type="datetime", nullable=true)
     */
    private ?DateTime $passwordChangedAt;

    public function __construct(string $email, string $encodedPassword) {
        $this->__UuidTraitConstructor();
        $this->email = $email;
        $this->password = $encodedPassword;
        $this->orders = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
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
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order): void
    {
        $this->orders[] = $order;
    }

    /**
     * @param Order $order
     */
    public function removeOrder(Order $order): void
    {
        $this->orders->removeElement($order);
    }

    /**
     * @param null|ArrayCollection $orders
     */
    public function setOrders(?ArrayCollection $orders): void
    {
        $this->orders = $orders;
    }

    public function getPasswordChangedAt(): ?DateTime
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(?DateTime $passwordChangedAt): void
    {
        $this->passwordChangedAt = $passwordChangedAt;
    }

    /**
     * CUSTOM
     */

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }
}
