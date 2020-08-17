<?php

namespace App\Entity;

use App\Repository\ShopCategoryRepository;
use App\Traits\CreatedAtTrait;
use App\Traits\EnableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ShopCategoryRepository::class)
 */
class ShopCategory
{
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
    private string $title;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * ShopCategory constructor.
     */
    public function __construct()
    {
        $this->__EnableTraitConstructor();
        $this->__CreatedAtTraitConstructor();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}
