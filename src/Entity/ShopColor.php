<?php

namespace App\Entity;

use App\Repository\ShopColorRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=ShopColorRepository::class)
 */
class ShopColor {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enable;

    /**
     * ShopColor constructor.
     */
    public function __construct() {
        $this->createdAt = new DateTime('now');
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->name;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEnable(): ?bool {
        return $this->enable;
    }

    public function setEnable(?bool $enable): self {
        $this->enable = $enable;

        return $this;
    }
}
