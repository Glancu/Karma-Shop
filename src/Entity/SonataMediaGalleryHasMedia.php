<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;

/**
 * @ORM\Entity
 * @ORM\Table(name="media__gallery_has_media")
 */
class SonataMediaGalleryHasMedia extends BaseGalleryHasMedia
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
