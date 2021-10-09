<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailHistoryRepository;
use App\Entity\Traits\CreatedAtTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="emails_history")
 * @ORM\Entity(repositoryClass=EmailHistoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class EmailHistory
{
    use CreatedAtTrait;

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
     * @ORM\Column(type="string", length=255)
     */
    private string $emailTo = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $subject = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private string $message = '';

    public function __construct(string $emailTo, string $subject, string $message)
    {
        $this->emailTo = $emailTo;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function __toString(): string
    {
        return $this->emailTo . ' - ' . $this->subject;
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
    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    /**
     * @param string $emailTo
     */
    public function setEmailTo(string $emailTo): void
    {
        $this->emailTo = $emailTo;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
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
}
