<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\ShopBrand;
use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ShopSerializeDataResponse
{
    private ShopNormalizer $shopNormalizer;
    private SerializerInterface $serializer;

    public function __construct(ShopNormalizer $shopNormalizer, SerializerInterface $serializer)
    {
        $this->shopNormalizer = $shopNormalizer;
        $this->serializer = $serializer;
    }

    /**
     * @param array $products
     * @param int $countProducts
     * @param array $parameters
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getShopProductsData(array $products, int $countProducts = 0, array $parameters = []): string
    {
        $items = [];

        /**
         * @var ShopProduct $product
         */
        foreach ($products as $product) {
            $data = $this->shopNormalizer->normalizeShopProducts($product, 'json', [
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

        $return = [
            'countItems' => $countProducts,
            'items' => $items
        ];

        if (isset($parameters['categorySlug']) && $parameters['categorySlug']) {
            $return['categoryName'] = $items[0]['shopCategory']['title'];
        }

        return $this->serializer->serialize($return, 'json');
    }

    /**
     * @param ShopProduct $product
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function getSingleShopProductData(ShopProduct $product): array
    {
        return $this->shopNormalizer->normalizeShopProducts($product, 'json', [
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
                'shop_color',
                'comment'
            ],
            'datetime_format' => 'Y-m-d H:i:s'
        ]);
    }

    public function getShopCategoriesList(array $categories): string
    {
        $items = '';

        $countItems = count($categories);

        foreach ($categories as $key => $category) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopNormalizer->normalizeCategoriesList($category, 'json', [
                    'groups' => [
                        'shop_category',
                        'uuid_trait',
                        'enable_trait'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if (!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if ($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if (substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }

    public function getShopBrandsList(array $brands): string
    {
        $items = '';

        $countItems = count($brands);

        /**
         * @var ShopBrand $brand
         */
        foreach ($brands as $key => $brand) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopNormalizer->normalizeBrandsList($brand, 'json', [
                    'groups' => [
                        'shop_brand'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if (!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if ($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if (substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }

    public function getShopColorsList(array $colors): string
    {
        $items = '';

        $countItems = count($colors);

        /**
         * @var ShopColor $color
         */
        foreach ($colors as $key => $color) {
            $normalizer = new GetSetMethodNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer([$normalizer], [$encoder]);

            try {
                $data = $this->shopNormalizer->normalizeColorsList($color, 'json', [
                    'groups' => [
                        'shop_color'
                    ]
                ]);
            } catch (ExceptionInterface $e) {
                throw new RuntimeException($e);
            }

            if (!empty($data) && is_array($data)) {
                $items .= $serializer->serialize($data, 'json');

                if ($key + 1 !== $countItems) {
                    $items .= ',';
                }
            }
        }

        if (substr($items, -1) === ',') {
            $items = substr($items, 0, -1);
        }

        return '[' . $items . ']';
    }
}
