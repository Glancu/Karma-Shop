<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
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
    private string $uuid = '';

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
