<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailTemplateRepository;
use App\Entity\Traits\CreatedAtTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
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

    public const TYPE_NEW_ORDER_TO_ADMIN = 1;
    public const TYPE_NEW_ORDER_TO_USER = 2;
    public const TYPE_NEW_CONTACT_TO_ADMIN = 3;
    public const TYPE_ORDER_NOT_PAID_TO_USER = 4;
    public const TYPE_ORDER_PAID_TO_USER = 5;
    public const TYPE_ORDER_SENT_PRODUCTS_TO_USER = 6;
    public const TYPE_ORDER_IN_PROGRESS_TO_USER = 7;
    public const TYPE_ORDER_PAID_TO_ADMIN = 8;

    public static function getTypesArr(): array
    {
        return [
            self::TYPE_NEW_ORDER_TO_ADMIN => 'Send mail to admin when user order an order',
            self::TYPE_NEW_ORDER_TO_USER => 'Send mail to user when user order an order',
            self::TYPE_NEW_CONTACT_TO_ADMIN => 'Send mail to admin when user contact from contact form',
            self::TYPE_ORDER_NOT_PAID_TO_USER => 'Send mail to user when order was not paid',
            self::TYPE_ORDER_PAID_TO_USER => 'Send mail to user when order was paid',
            self::TYPE_ORDER_PAID_TO_ADMIN => 'Send mail to admin when order was paid',
            self::TYPE_ORDER_SENT_PRODUCTS_TO_USER => 'Send mail to user when order products was sent',
            self::TYPE_ORDER_IN_PROGRESS_TO_USER => 'Send mail to user when order status is in progress',
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
