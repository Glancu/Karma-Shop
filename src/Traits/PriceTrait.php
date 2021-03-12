<?php
declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait PriceTrait
{
    /**
     * @Groups("price_trait")
     *
     * @ORM\Column(name="price_net", type="integer", nullable=true)
     */
    private ?int $priceNet;

    /**
     * @Groups("price_trait")
     *
     * @ORM\Column(name="price_gross", type="integer", nullable=true)
     */
    private ?int $priceGross;

    /**
     * @return null|int
     */
    public function getPriceNet(): ?int
    {
        return $this->priceNet;
    }

    /**
     * @param int $priceNet
     */
    public function setPriceNet(int $priceNet): void
    {
        $this->priceNet = $priceNet;
    }

    /**
     * @return null|int
     */
    public function getPriceGross(): ?int
    {
        return $this->priceGross;
    }

    /**
     * @param int $priceGross
     */
    public function setPriceGross(int $priceGross): void
    {
        $this->priceGross = $priceGross;
    }
}
