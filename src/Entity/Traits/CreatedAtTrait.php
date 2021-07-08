<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait CreatedAtTrait
{
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * CreatedAtTrait constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime('now');
    }

    /**
     * @return null|DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
}
