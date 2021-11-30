<?php

namespace App\Serializer;

use App\Entity\BlogPost;
use App\Entity\ClientUser;
use App\Entity\Contact;
use App\Entity\Newsletter;
use App\Entity\ShopBrand;
use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use DateTime;
use DateTimeInterface;
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
    private ShopNormalizer $shopNormalizer;
    private BlogNormalizer $blogNormalizer;

    public function __construct(
        SerializerInterface $serializer,
        ShopNormalizer $shopNormalizer,
        BlogNormalizer $blogNormalizer
    ) {
        $this->serializer = $serializer;
        $this->shopNormalizer = $shopNormalizer;
        $this->blogNormalizer = $blogNormalizer;
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
                'createdAt' => $dateCallback
            ],
        ];

        $ignoredAttributes = [
            'password',
            'orders',
            'roles',
            'comments',
            'id',
            'salt',
            'username',
            'passwordChangedAt'
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

        if(isset($parameters['categorySlug']) && $parameters['categorySlug']) {
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

    /**
     * @param array $posts
     * @param int $countPosts
     * @param array $parameters
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogPostsData(array $posts, int $countPosts = 0, array $parameters = []): string
    {
        $items = [];

        /**
         * @var BlogPost $post
         */
        foreach ($posts as $post) {
            $data = $this->blogNormalizer->normalizeBlogPostsList($post, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_post_list',
                    'blog_category_list',
                    'blog_tag_list'
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);

            $items[] = $data;
        }

        $return = [
            'countItems' => $countPosts,
            'items' => $items
        ];

        if($parameters['category']) {
            $return['categoryName'] = $items[0]['category']['name'];
        }

        if($parameters['tag']) {
            foreach($items[0]['tags'] as $tag) {
                if($parameters['tag'] === $tag['slug']) {
                    $return['tagName'] = $tag['name'];
                }
            }
        }

        return $this->serializer->serialize($return, 'json');
    }

    /**
     * @param BlogPost $post
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function getSingleBlogPostData(BlogPost $post): array
    {
        return $this->blogNormalizer->normalizeSingleBlogPost($post, 'json', [
            'groups' => [
                'uuid_trait',
                'enable_trait',
                'price_trait',
                'blog_post_show',
                'blog_category_show',
                'blog_tag_show',
                'comment'
            ],
            'datetime_format' => 'Y-m-d H:i:s'
        ]);
    }

    /**
     * @param array $posts
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogPopularPosts(array $posts): string
    {
        $items = [];

        /**
         * @var BlogPost $post
         */
        foreach ($posts as $post) {
            $data = $this->blogNormalizer->normalizeBlogPopularPosts($post, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_post_list',
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);

            $items[] = $data;
        }

        return $this->serializer->serialize($items, 'json');
    }

    /**
     * @param array $categories
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogCategoriesLatestData(array $categories): string
    {
        $items = [];

        foreach ($categories as $category) {
            $items[] = $this->blogNormalizer->normalizeBlogCategoriesLatest($category, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_category_show'
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);
        }

        return $this->serializer->serialize($items, 'json');
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
        return $innerObject instanceof DateTime ? $innerObject->format(DateTimeInterface::ATOM) : '';
    }
}
