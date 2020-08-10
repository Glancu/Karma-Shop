<?php

namespace App\Entity;

use App\Repository\ShopBrandRepository;
use App\Traits\EnableTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ShopBrandRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ShopBrand {
    use EnableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * ShopBrand constructor.
     */
    public function __construct() {
        $this->createdAt = new DateTime('now');
        $this->enable = true;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->title;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }
}
