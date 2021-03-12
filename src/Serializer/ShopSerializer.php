<?php

namespace App\Serializer;

use App\Entity\ShopBrand;
use App\Entity\ShopCategory;
use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use App\Entity\SonataMediaMedia;
use App\Service\MoneyService;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ShopSerializer
{
    private $router;
    private $normalizer;
    private $imageProvider;
    private $request;
    private MoneyService $moneyService;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        ImageProvider $imageProvider,
        RequestStack $request,
        MoneyService $moneyService
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->imageProvider = $imageProvider;
        $this->request = $request;
        $this->moneyService = $moneyService;
    }

    public function normalizeProductsList(ShopProduct $topic, $format = null, array $context = []): array
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
                }
            }
        }

        if (isset($data['shopCategory'], $data['shopCategory']['enable']) && $data['shopCategory']['enable'] === false) {
            return [];
        }

        if ($topic->getImages()->count() > 0) {
            $data['images'] = [];
        }

        /**
         * @var SonataMediaMedia $image
         */
        foreach ($topic->getImages() as $image) {
            $request = $this->request->getCurrentRequest();
            $provider = $this->imageProvider;
            $format = $provider->getFormatName($image, 'big');
            $imageUrl = $provider->generatePublicUrl($image, $format);

            $baseUrl = $request ? ($request->getSchemeAndHttpHost() . $request->getBaseUrl()) : '';

            $fullImageUrl = $baseUrl . $imageUrl;

            $data['images'][] = [
                'name' => $image->getName(),
                'url' => $fullImageUrl
            ];
        }

        if(isset($data['priceNet'], $data['priceGross'])) {
            $data['priceNet'] = $moneyService->convertIntToFloatWithCurrency($data['priceNet']);
            $data['priceGross'] = $moneyService->convertIntToFloatWithCurrency($data['priceGross']);
        }

        if(isset($data['shopDelivery'], $data['shopDelivery']['priceNet'], $data['shopDelivery']['priceGross'])) {
            $shopDelivery = $data['shopDelivery'];

            $shopDelivery['priceNet'] = $moneyService->convertIntToFloatWithCurrency($shopDelivery['priceNet']);
            $shopDelivery['priceGross'] = $moneyService->convertIntToFloatWithCurrency($shopDelivery['priceGross']);

            $data['shopDelivery'] = $shopDelivery;
        }

        return $data;
    }

    /**
     * @param ShopCategory $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeCategoriesList($topic, $format = null, array $context = []): array
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

    /**
     * @param ShopBrand $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeBrandsList($topic, $format = null, array $context = []): array
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

    /**
     * @param ShopColor $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeColorsList($topic, $format = null, array $context = []): array
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
