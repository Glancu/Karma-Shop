<?php

namespace App\Entity;

use App\Component\OrderStatus;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\PriceTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\OrderRepository;
use App\Service\GeneratorStringService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Exception;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 */
class Order
{
    use CreatedAtTrait, UuidTrait, PriceTrait {
        UuidTrait::__construct as private __UuidTraitConstructor;
    }

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProduct")
     */
    private $products;

    /**
     * @var ClientUser
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientUser", inversedBy="orders")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private ClientUser $user;

    /**
     * @ORM\Embedded(class="App\Entity\OrderPersonalDataInfo", columnPrefix=false)
     */
    private OrderPersonalDataInfo $orderPersonalDataInfo;

    /**
     * @ORM\Embedded(class="App\Entity\OrderAddress", columnPrefix=false)
     */
    private OrderAddress $orderAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="additional_information", type="text", nullable=true)
     */
    private ?string $additionalInformation;

    /**
     * @var int
     *
     * @ORM\Column(name="method_pay", type="integer")
     */
    private int $methodPayment;

    /**
     * @var int
     *
     * @ORM\Column(name="status_payment", type="smallint")
     */
    private int $statusPayment;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15)
     */
    private string $orderNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private int $status;

    /**
     * Order constructor.
     *
     * @param ClientUser $clientUser
     * @param OrderPersonalDataInfo $orderPersonalDataInfo
     * @param OrderAddress $orderAddress
     * @param int $methodPayment
     * @param $products
     * @param string|null $additionalInformation
     *
     * @throws Exception
     */
    public function __construct(
        ClientUser $clientUser,
        OrderPersonalDataInfo $orderPersonalDataInfo,
        OrderAddress $orderAddress,
        int $methodPayment,
        $products,
        ?string $additionalInformation = null
    ) {
        $this->__UuidTraitConstructor();
        $this->statusPayment = self::STATUS_PAYMENT_NEW;
        $this->orderPersonalDataInfo = $orderPersonalDataInfo;
        $this->orderAddress = $orderAddress;
        $this->user = $clientUser;
        $this->methodPayment = $methodPayment;
        $this->addShopProductsArr($products);
        $this->additionalInformation = $additionalInformation;
        $this->orderNumber = GeneratorStringService::generateString(15);
        $this->generateTotalPrices();
        $this->status = OrderStatus::STATUS_NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMethodPayment(): int
    {
        return $this->methodPayment;
    }

    /**
     * @param int $methodPayment
     */
    public function setMethodPayment(int $methodPayment): void
    {
        $this->methodPayment = $methodPayment;
    }

    /**
     * @return int
     */
    public function getStatusPayment(): int
    {
        return $this->statusPayment;
    }

    /**
     * @param int $statusPayment
     */
    public function setStatusPayment(int $statusPayment): void
    {
        $this->statusPayment = $statusPayment;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ShopProduct $shopProduct
     */
    public function addShopProduct(ShopProduct $shopProduct): void
    {
        $this->products[] = $shopProduct;
    }

    /**
     * @param ShopProduct $shopProduct
     */
    public function removeShopProduct(ShopProduct $shopProduct): void
    {
        $this->products->removeElement($shopProduct);
    }

    /**
     * @return ClientUser
     */
    public function getUser(): ClientUser
    {
        return $this->user;
    }

    /**
     * @param ClientUser $user
     */
    public function setUser(ClientUser $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string|null
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    /**
     * @param string|null $additionalInformation
     */
    public function setAdditionalInformation(?string $additionalInformation): void
    {
        $this->additionalInformation = $additionalInformation;
    }

    /**
     * @return OrderPersonalDataInfo
     */
    public function getOrderPersonalDataInfo(): OrderPersonalDataInfo
    {
        return $this->orderPersonalDataInfo;
    }

    /**
     * @param OrderPersonalDataInfo $orderPersonalDataInfo
     */
    public function setOrderPersonalDataInfo(OrderPersonalDataInfo $orderPersonalDataInfo): void
    {
        $this->orderPersonalDataInfo = $orderPersonalDataInfo;
    }

    /**
     * @return OrderAddress
     */
    public function getOrderAddress(): OrderAddress
    {
        return $this->orderAddress;
    }

    /**
     * @param OrderAddress $orderAddress
     */
    public function setOrderAddress(OrderAddress $orderAddress): void
    {
        $this->orderAddress = $orderAddress;
    }

    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * CUSTOM
     */

    public const METHOD_PAYMENT_TYPE_ONLINE = 1;
    public const METHOD_PAYMENT_TYPE_PAYPAL = 2;

    public static function getMethodPaymentsArr(): array
    {
        return [
            self::METHOD_PAYMENT_TYPE_ONLINE => 'Online',
            self::METHOD_PAYMENT_TYPE_PAYPAL => 'PayPal'
        ];
    }

    public function getMethodPaymentStr(): ?string
    {
        return $this->methodPayment ? self::getMethodPaymentsArr()[$this->methodPayment] : null;
    }

    public static function getMethodPaymentInt(string $methodPayment):int
    {
        return array_search(trim($methodPayment), self::getMethodPaymentsArr());
    }

    public const STATUS_PAYMENT_NEW = 1;
    public const STATUS_PAYMENT_PAID = 2;

    public function getStatusPaymentsArr(): array
    {
        return [
            self::STATUS_PAYMENT_NEW => 'New',
            self::STATUS_PAYMENT_PAID => 'Paid'
        ];
    }

    public function getStatusPaymentStr(): ?string
    {
        return $this->statusPayment ? $this->getStatusPaymentsArr()[$this->statusPayment] : null;
    }

    private function addShopProductsArr($products): void
    {
        /**
         * @var ShopProduct $product
         */
        foreach ($products as $product) {
            $this->addShopProduct($product);
        }
    }

    private function generateTotalPrices(): void {
        $priceNet = 0;
        $priceGross = 0;

        /**
         * @var ShopProduct $product
         */
        foreach($this->getProducts() as $product) {
            $priceNet += $product->getPriceNet();
            $priceGross += $product->getPriceGross();
        }

        $this->setPriceNet($priceNet);
        $this->setPriceGross($priceGross);
    }

    public static function replaceVariablesForEmail(Order $order, string $text): string
    {
        return str_replace(
            [
                '%order_number%',
                '%client_email%',
                '%method_payment%'
            ],
            [
                $order->getOrderNumber(),
                $order->getUser()->getEmail(),
                $order->getMethodPaymentStr()
            ],
            $text
        );
    }
}
