<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\DataProcessingAgreement;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
{
    use CreatedAtTrait, DataProcessingAgreement {
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
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
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column()
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
     * @ORM\Column()
     */
    private string $email = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column()
     */
    private string $subject = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="text")
     */
    private string $message = '';

    /**
     * Contact constructor.
     */
    public function __construct(
        string $name,
        string $email,
        string $subject,
        string $message,
        bool $dataProcessingAgreement = false
    ) {
        $this->__CreatedAtTraitConstructor();
        $this->__DPAConstructor();
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dataProcessingAgreement = $dataProcessingAgreement;
    }

    public function __toString(): string
    {
        return $this->email;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
