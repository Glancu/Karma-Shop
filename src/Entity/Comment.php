<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use App\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    use CreatedAtTrait, EnableTrait, UuidTrait {
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
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $subject = null;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="text")
     */
    private string $text = '';

    public function __construct(string $name, string $email, string $text, ?string $subject = '', bool $enable = true)
    {
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->email = $email;
        $this->text = $text;
        $this->subject = $subject;
        $this->enable = $enable;
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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
