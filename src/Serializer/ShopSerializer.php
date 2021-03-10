<?php

namespace App\Serializer;

use App\Entity\ShopBrand;
use App\Entity\ShopCategory;
use App\Entity\ShopColor;
use App\Entity\SonataMediaMedia;
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

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        ImageProvider $imageProvider,
        RequestStack $request
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->imageProvider = $imageProvider;
        $this->request = $request;
    }

    public function normalizeProductsList($topic, $format = null, array $context = []): array
    {
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
