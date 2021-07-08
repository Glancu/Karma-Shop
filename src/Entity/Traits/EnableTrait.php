<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private bool $enable = true;

    /**
     * EnableTrait constructor.
     */
    public function __construct()
    {
        $this->enable = true;
    }

    /**
     * @return null|bool
     */
    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }
}
