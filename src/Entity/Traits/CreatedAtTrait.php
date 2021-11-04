<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait CreatedAtTrait
{
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Groups("createdAt_trait")
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
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
