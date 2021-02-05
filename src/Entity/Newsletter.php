<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\NewsletterRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\DataProcessingAgreement;
use App\Traits\EnableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NewsletterRepository::class)
 */
class Newsletter
{
    use EnableTrait, CreatedAtTrait, DataProcessingAgreement {
        EnableTrait::__construct as private __EnableTraitConstructor;
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
        DataProcessingAgreement::__construct as private __DPAConstructor;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private ?string $name = null;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     *
     * @ORM\Column()
     */
    private string $email;

    /**
     * Newsletter constructor.
     */
    public function __construct()
    {
        $this->__EnableTraitConstructor();
        $this->__CreatedAtTraitConstructor();
        $this->__DPAConstructor();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
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
}
