<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\NewsletterRepository;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\DataProcessingAgreement;
use App\Entity\Traits\EnableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NewsletterRepository::class)
 */
class Newsletter
{
    use EnableTrait, CreatedAtTrait, DataProcessingAgreement {
        EnableTrait::__construct as private __EnableTraitConstructor;
        DataProcessingAgreement::__construct as private __DPAConstructor;
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
    private string $email = '';

    public function __construct(string $email, bool $dataProcessingAgreement = false, string $name = null)
    {
        $this->__EnableTraitConstructor();
        $this->__DPAConstructor();
        $this->email = $email;
        $this->dataProcessingAgreement = $dataProcessingAgreement;
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
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
}
