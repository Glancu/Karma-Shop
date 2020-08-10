<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use App\Traits\CreatedAtTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Contact {
    use CreatedAtTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

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
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column()
     */
    private string $subject;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(type="text")
     */
    private string $message;

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->email;
    }

    /**
     * @return null|int
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void {
        $this->message = $message;
    }
}
