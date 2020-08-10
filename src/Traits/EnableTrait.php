<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait {
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enable;

    /**
     * @return null|bool
     */
    public function isEnable(): ?bool {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void {
        $this->enable = $enable;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->enable = false;
    }
}
