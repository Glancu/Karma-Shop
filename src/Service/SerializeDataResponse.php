<?php

namespace App\Service;

use App\Entity\ClientUser;
use App\Entity\Contact;
use App\Entity\Newsletter;
use App\Entity\ShopBrand;
use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use App\Serializer\ShopSerializer;
use DateTime;
use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializeDataResponse
{
    private SerializerInterface $serializer;

    private ShopSerializer $shopSerializer;

    public function __construct(SerializerInterface $serializer, ShopSerializer $shopSerializer)
    {
        $this->serializer = $serializer;
        $this->shopSerializer = $shopSerializer;
    }

    /**
     * @param ClientUser $clientUser
     *
     * @return string
     */
    public function getClientUserData(ClientUser $clientUser): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $ignoredAttributes = [
            'password',
            'orders',
            'roles',
            'comments',
            'id',
            'salt',
            'username'
        ];

        return $this->getSerializedData($clientUser, $ignoredAttributes, $defaultContext);
    }

    /**
     * @param Newsletter $newsletter
     *
     * @return string
     */
    public function getNewsletterData(Newsletter $newsletter): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $ignoredAttributes = [
            'name',
            'id'
        ];

        if ($newsletter->getName()) {
            unset($ignoredAttributes[0]);
        }

        return $this->getSerializedData($newsletter, $ignoredAttributes, $defaultContext);
    }

    /**
     * @param Contact $contact
     *
     * @return string
     */
    public function getContactData(Contact $contact): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback
            ],
        ];

        $ignoreAttributes = [
            'id'
        ];

        return $this->getSerializedData($contact, $ignoreAttributes, $defaultContext);
    }

    /**
     * @param array $products
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getProductsData(array $products): string
    {
        $items = [];

        /**
         * @var ShopProduct $product
         */
        foreach ($products as $product) {
            $data = $this->shopSerializer->normalizeProductsList($product, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'price_trait',
                    'shop_product',
                    'shop_category',
                    'shop_delivery',
                    'product_review',
                    'shop_product_specification',
                    'shop_product_specification_type',
                    'shop_brand',
                    'shop_color'
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);

            $items[] = $data;
        }

        return $this->serializer->serialize($items, 'json');
    }

    public function getCategoriesList(array $categories): string
    {
        $items = '';

        $countItems = count($categories);

        foreach($categories as $key => $category) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopSerializer->normalizeCategoriesList($category, 'json', [
                    'groups' => [
                        'shop_category',
                        'uuid_trait',
                        'enable_trait'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if(!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if(substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }

    public function getBrandsList(array $brands): string
    {
        $items = '';

        $countItems = count($brands);

        /**
         * @var ShopBrand $brand
         */
        foreach($brands as $key => $brand) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopSerializer->normalizeBrandsList($brand, 'json', [
                    'groups' => [
                        'shop_brand'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if(!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if(substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }

    public function getColorsList(array $colors): string
    {
        $items = '';

        $countItems = count($colors);

        /**
         * @var ShopColor $color
         */
        foreach($colors as $key => $color) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopSerializer->normalizeColorsList($color, 'json', [
                    'groups' => [
                        'shop_color'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if(!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if(substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }

    /**
     * @param $object
     * @param array|null $ignoredAttributes
     * @param array $defaultContext
     *
     * @return string|null
     */
    private function getSerializedData($object, ?array $ignoredAttributes, $defaultContext = []): ?string
    {
        if (!$object) {
            return null;
        }

        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);
        $encoder = new JsonEncoder();

        $serializer = new Serializer([$normalizer], [$encoder]);

        return $serializer->serialize($object, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => $ignoredAttributes
        ]);
    }

    /**
     * @param $innerObject
     *
     * @return string
     */
    private static function getDateCallback($innerObject): string
    {
        return $innerObject instanceof DateTime ? $innerObject->format(DateTime::ATOM) : '';
    }
}
