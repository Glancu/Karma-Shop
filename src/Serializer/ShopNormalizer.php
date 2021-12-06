<?php

namespace App\Serializer;

use App\Entity\ProductReview;
use App\Entity\ShopProduct;
use App\Service\ImageService;
use App\Service\MoneyService;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ShopNormalizer extends BaseNormalizer
{
    private UrlGeneratorInterface $router;
    private ObjectNormalizer $normalizer;
    private ImageProvider $imageProvider;
    private MoneyService $moneyService;
    private ImageService $imageService;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        ImageProvider $imageProvider,
        MoneyService $moneyService,
        ImageService $imageService
    ) {
        parent::__construct($imageProvider, $imageService);

        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->imageProvider = $imageProvider;
        $this->moneyService = $moneyService;
        $this->imageService = $imageService;
    }

    /**
     * @param ShopProduct $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeShopProducts(ShopProduct $topic, $format = null, array $context = []): array
    {
        $moneyService = $this->moneyService;

        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        if (isset($data['shopProductSpecifications'])) {
            foreach ($data['shopProductSpecifications'] as $key => $shopProductSpecification) {
                if (isset($shopProductSpecification['shopProductSpecificationType'], $shopProductSpecification['shopProductSpecificationType']['name'])) {
                    $shopProductSpecification['name'] = $shopProductSpecification['shopProductSpecificationType']['name'];
                    unset($shopProductSpecification['shopProductSpecificationType']);

                    $data['shopProductSpecifications'][$key] = $shopProductSpecification;
                }
            }
        }

        if (isset($data['reviews'])) {
            foreach ($data['reviews'] as $reviewKey => $review) {
                if (isset($review['enable']) && $review['enable'] === false) {
                    unset($data['reviews'][$reviewKey]);
                } else {
                    /**
                     * @var ProductReview $reviewObj
                     */
                    $reviewObj = $topic->getReviews()->filter(function($reviewObject) use ($review) {
                        return $reviewObject->getUuid() === $review['uuid'];
                    })->first();
                    if($reviewObj && $reviewObj->getCreatedAt()) {
                        $data['reviews'][$reviewKey]['createdAt'] = $reviewObj->getCreatedAt()->format('d-m-Y H:i:s');
                    } else {
                        unset($data['reviews'][$reviewKey]);
                    }
                }
            }

            $data['reviews'] = array_values($data['reviews']);
        }

        if (isset($data['shopCategory'], $data['shopCategory']['enable']) && $data['shopCategory']['enable'] === false) {
            return [];
        }

        $data['images'] = $this->getUrlImages($topic->getImages());

        if(isset($data['priceNet'], $data['priceGross'])) {
            $data['priceNet'] = $moneyService->convertIntToFloat($data['priceNet']);
            $data['priceGross'] = $moneyService->convertIntToFloat($data['priceGross']);
        }

        $data = $this->generateCommentsForSingleObject($topic, $data);

        return $data;
    }

    /**
     * @param $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeCategoriesList($topic, $format = null, array $context = []): array
    {
        return $this->getSimpleNormalizeWithCountProducts($topic, $format, $context);
    }

    /**
     * @param $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeBrandsList($topic, $format = null, array $context = []): array
    {
        return $this->getSimpleNormalizeWithCountProducts($topic, $format, $context);
    }

    /**
     * @param $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeColorsList($topic, $format = null, array $context = []): array
    {
        return $this->getSimpleNormalizeWithCountProducts($topic, $format, $context);
    }

    /**
     * @param $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeShopProductSearchList($topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        $data['image'] = $this->getUrlImages($topic->getImages(), 'small')[0];

        return $data;
    }

    /**
     * @param $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    private function getSimpleNormalizeWithCountProducts($topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        if($topic->getCountProducts() === 0) {
            return [];
        }

        $data['countProducts'] = $topic->getCountProducts();

        return $data;
    }
}
