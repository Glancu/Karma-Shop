<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PayPalTransactionRepository;
use Beelab\PaypalBundle\Entity\Transaction as BaseTransaction;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pay_pal_transactions")
 * @ORM\Entity(repositoryClass=PayPalTransactionRepository::class)
 */
class PayPalTransaction extends BaseTransaction
{
    /**
     * @var Order|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Order", mappedBy="payPalTransaction")
     */
    private ?Order $order;

    public function __toString(): string
    {
        return $this->id . ' - ' . $this->getStatusLabel();
    }

    public function getDescription(): ?string
    {
        // here you can return a generic description, if you don't want to list items
        return $this->getOrder() ? $this->getOrder()->getUuid() : '';
    }

    public function getShippingAmount(): string
    {
        // here you can return shipping amount. This amount MUST be already in your total amount
        return $this->getOrder() ? (string)$this->getOrder()->getPriceGross() : '';
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }
}
