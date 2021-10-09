<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailTemplateRepository;
use App\Entity\Traits\CreatedAtTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="email_templates")
 * @ORM\Entity(repositoryClass=EmailTemplateRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class EmailTemplate
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
    private string $name = '';

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

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private ?int $type = null;

    public function __construct(string $name, string $subject, string $message, int $type)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
        $this->type = $type;
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

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * CUSTOM
     */

    public static int $TYPE_NEW_ORDER_TO_ADMIN = 1;
    public static int $TYPE_NEW_ORDER_TO_USER = 2;
    public static int $TYPE_NEW_CONTACT_TO_ADMIN = 3;

    public static function getTypesArr(): array
    {
        return [
            self::$TYPE_NEW_ORDER_TO_ADMIN => 'Send mail to admin when user order an order',
            self::$TYPE_NEW_ORDER_TO_USER => 'Send mail to user when user order an order',
            self::$TYPE_NEW_CONTACT_TO_ADMIN => 'Send mail to admin when user contact from contact form'
        ];
    }

    public function getTypeStr(): ?string
    {
        if(!$this->type) {
            return null;
        }
        return self::getTypesArr()[$this->type];
    }
}
