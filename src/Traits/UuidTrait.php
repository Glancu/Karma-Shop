<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UuidTrait
{
    /**
     * @var string
     *
     * @Groups("uuid_trait")
     *
     * @ORM\Column(type="string", unique=true)
     */
    private string $uuid;

    public function __construct()
    {
        $this->uuid = uuid_create(UUID_TYPE_RANDOM);
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
