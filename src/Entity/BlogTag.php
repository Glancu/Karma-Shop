<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\BlogTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="blog_tags")
 * @ORM\Entity(repositoryClass=BlogTagRepository::class)
 */
class BlogTag
{
    use CreatedAtTrait, UuidTrait {
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
     * @Groups({"blog_tag_list", "blog_tag_show"})
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private string $name = '';

    /**
     * @var string
     *
     * @Groups({"blog_tag_list", "blog_tag_show"})
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-")
     *
     * @ORM\Column(name="slug", type="string")
     */
    private string $slug;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\BlogPost", mappedBy="tags")
     */
    private $posts;

    public function __construct(string $name)
    {
        $this->__UuidTraitConstructor();
        $this->name = $name;
        $this->posts = new ArrayCollection();
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
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param BlogPost $post
     */
    public function addPost(BlogPost $post): void
    {
        $this->posts[] = $post;
    }

    /**
     * @param BlogPost $post
     */
    public function removePost(BlogPost $post): void
    {
        $this->posts->removeElement($post);
    }

    /**
     * CUSTOM
     */

    public function getCountPosts(): int
    {
        return $this->posts->count();
    }
}
