<?php

namespace App\Entity;

use App\Component\OrderStatus;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\PriceTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\OrderRepository;
use App\Service\MoneyService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Exception;

/**
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
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
     * @ORM\Column(name="status", type="smallint")
     */
    private int $status;

    /**
     * @var PayPalTransaction|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PayPalTransaction", inversedBy="order")
     * @ORM\JoinColumn(name="pay_pal_transaction_id", referencedColumnName="id")
     */
    private ?PayPalTransaction $payPalTransaction;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShopProductItem")
     * @ORM\JoinTable(name="orders_shop_product_items",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="shop_product_item_id", referencedColumnName="id")}
     *      )
     */
    private $shopProductItems;

    /**
     * Order constructor.
     *
     * @param ClientUser $clientUser
     * @param OrderPersonalDataInfo $orderPersonalDataInfo
     * @param OrderAddress $orderAddress
     * @param int $methodPayment
     * @param $productsItems
     * @param string|null $additionalInformation
     *
     * @throws Exception
     */
    public function __construct(
        ClientUser $clientUser,
        OrderPersonalDataInfo $orderPersonalDataInfo,
        OrderAddress $orderAddress,
        int $methodPayment,
        $productsItems,
        ?string $additionalInformation = null
    ) {
        $this->__UuidTraitConstructor();
        $this->orderPersonalDataInfo = $orderPersonalDataInfo;
        $this->orderAddress = $orderAddress;
        $this->user = $clientUser;
        $this->methodPayment = $methodPayment;
        $this->addShopProductsItemsArr($productsItems);
        $this->additionalInformation = $additionalInformation;
        $this->generateTotalPrices();
        $this->status = OrderStatus::STATUS_NEW;
    }

    public function __toString(): string
    {
        return $this->uuid;
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
     * @return PayPalTransaction|null
     */
    public function getTransaction(): ?PayPalTransaction
    {
        return $this->payPalTransaction;
    }

    /**
     * @param PayPalTransaction|null $payPalTransaction
     */
    public function setTransaction(?PayPalTransaction $payPalTransaction): void
    {
        $this->payPalTransaction = $payPalTransaction;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getShopProductItems()
    {
        return $this->shopProductItems;
    }

    /**
     * @param ShopProductItem $shopProductItem
     */
    public function addShopProductItem(ShopProductItem $shopProductItem): void
    {
        $this->shopProductItems[] = $shopProductItem;
    }

    /**
     * @param ShopProductItem $shopProductItem
     */
    public function removeShopProductItem(ShopProductItem $shopProductItem): void
    {
        $this->shopProductItems->removeElement($shopProductItem);
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

    private function addShopProductsItemsArr($productsItems): void
    {
        /**
         * @var ShopProductItem $productsItem
         */
        foreach ($productsItems as $productsItem) {
            $this->addShopProductItem($productsItem);
        }
    }

    private function generateTotalPrices(): void
    {
        $priceNet = 0;
        $priceGross = 0;

        /**
         * @var ShopProductItem $productItem
         */
        foreach ($this->getShopProductItems() as $productItem) {
            $product = $productItem->getProduct();
            if ($product) {
                $priceNet += $product->getPriceNet() * $productItem->getQuantity();
                $priceGross += $product->getPriceGross() * $productItem->getQuantity();
            }
        }

        $this->setPriceNet($priceNet);
        $this->setPriceGross($priceGross);
    }

    public function getStatusStr(): ?string
    {
        if ($this->status === null || $this->status === 0) {
            return null;
        }

        return OrderStatus::getStatusStr($this->status);
    }

    public function isMethodPayPal(): bool
    {
        return $this->getMethodPayment() === Order::METHOD_PAYMENT_TYPE_PAYPAL;
    }

    public function getPriceNetFloat(): float
    {
        return MoneyService::convertIntToFloat($this->getPriceNet());
    }

    public function getPriceGrossFloat(): float
    {
        return MoneyService::convertIntToFloat($this->getPriceGross());
    }
}
