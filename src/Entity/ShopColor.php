<?php

namespace App\Entity;

use App\Repository\ShopColorRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShopColorRepository::class)
 */
class ShopColor {
    use EnableTrait, CreatedAtTrait {
        EnableTrait::__construct as private __EnableTraitConstructor;
        CreatedAtTrait::__construct as private __CreatedAtTraitConstructor;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * ShopColor constructor.
     */
    public function __construct() {
        $this->__EnableTraitConstructor();
        $this->__CreatedAtTraitConstructor();
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
}
