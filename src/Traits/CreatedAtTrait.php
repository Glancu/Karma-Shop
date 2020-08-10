<?php

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait {
    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return null|DateTime
     */
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime();
    }
}
