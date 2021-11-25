<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="blog_posts")
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
class BlogPost
{
    use CreatedAtTrait, UuidTrait, EnableTrait {
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
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private string $title = '';

    /**
     * @var string
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-")
     *
     * @ORM\Column(name="slug", type="string")
     */
    private string $slug;

    /**
     * @var string
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\Column(name="short_content", type="text", nullable=false)
     */
    private string $shortContent = '';

    /**
     * @var string
     *
     * @Groups({"blog_post_show"})
     *
     * @ORM\Column(name="long_content", type="text", nullable=false)
     */
    private string $longContent = '';

    /**
     * @var int
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\Column(name="views", type="integer")
     */
    private int $views;

    /**
     * @var BlogCategory
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogCategory", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private ?BlogCategory $category = null;

    /**
     * @var AdminUser
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\AdminUser")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private ?AdminUser $author = null;

    /**
     * @var SonataMediaMedia
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SonataMediaMedia")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    private ?SonataMediaMedia $image = null;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @Groups({"blog_post_show"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment")
     * @ORM\JoinTable(name="blog_posts_comments",
     *      joinColumns={@ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")}
     * )
     */
    private $comments;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @Groups({"blog_post_list", "blog_post_show"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\BlogTag", inversedBy="posts")
     * @ORM\JoinTable(name="blog_posts_tags",
     *      joinColumns={@ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tags;

    public function __construct(
        string $title,
        string $shortContent,
        string $longContent,
        BlogCategory $blogCategory,
        AdminUser $author,
        SonataMediaMedia $image,
        array $tags,
        array $comments = [],
        bool $enable = true
    ) {
        $this->__UuidTraitConstructor();
        $this->title = $title;
        $this->shortContent = $shortContent;
        $this->longContent = $longContent;
        $this->category = $blogCategory;
        $this->author = $author;
        $this->image = $image;
        $this->views = 0;
        $this->comments = $comments;
        $this->enable = $enable;

        $this->generateTagsFromArray($tags);
    }

    public function __toString(): string
    {
        return $this->title;
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
     * @return string
     */
    public function getShortContent(): string
    {
        return $this->shortContent;
    }

    /**
     * @param string $shortContent
     */
    public function setShortContent(string $shortContent): void
    {
        $this->shortContent = $shortContent;
    }

    /**
     * @return string
     */
    public function getLongContent(): string
    {
        return $this->longContent;
    }

    /**
     * @param string $longContent
     */
    public function setLongContent(string $longContent): void
    {
        $this->longContent = $longContent;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return BlogCategory|null
     */
    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    /**
     * @param BlogCategory $category
     */
    public function setCategory(BlogCategory $category): void
    {
        $this->category = $category;
    }

    /**
     * @return AdminUser|null
     */
    public function getAuthor(): ?AdminUser
    {
        return $this->author;
    }

    /**
     * @param AdminUser $author
     */
    public function setAuthor(AdminUser $author): void
    {
        $this->author = $author;
    }

    /**
     * @return SonataMediaMedia|null
     */
    public function getImage(): ?SonataMediaMedia
    {
        return $this->image;
    }

    /**
     * @param SonataMediaMedia $image
     */
    public function setImage(SonataMediaMedia $image): void
    {
        $this->image = $image;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param BlogTag $tag
     */
    public function addTag(BlogTag $tag): void
    {
        if(!$this->tags) {
            $this->tags[] = $tag;
        }

        if(is_array($this->tags) && !in_array($tag, $this->tags, true)) {
            $this->tags[] = $tag;
        }

        if($this->tags instanceof PersistentCollection && !$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @param BlogTag $tag
     */
    public function removeTag(BlogTag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * CUSTOM
     */

    public function generateTagsFromArray($tags): void
    {
        foreach($tags as $tag) {
            $this->addTag($tag);
        }
    }
}
