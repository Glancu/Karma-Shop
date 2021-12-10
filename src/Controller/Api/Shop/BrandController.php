<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ShopBrand;
use App\Service\RedisCacheService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BrandController
 *
 * @package App\Controller\Shop
 *
 * @Route("/shop/brands")
 *
 * @OA\Tag(name="Shop")
 */
class BrandController
{
    private RedisCacheService $redisCacheService;

    public function __construct(
        RedisCacheService $redisCacheService
    ) {
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/list", name="shop_brands_list", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of brands",
     *     @OA\JsonContent(
     *          type="string", example={{"title": "Gionee", "slug": "gionee", "countProducts": 6 }, { "title": "Asus", "slug": "asus", "countProducts": 2 }},
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getShopBrandsList(): JsonResponse
    {
        $response = $this->redisCacheService->getAndSaveIfNotExistWithSerializeData(
            'shop.brand.findAllEnable',
            ShopBrand::class,
            'findAllEnable',
            'shopSerializeDataResponse',
            'getShopBrandsList'
        );
        if(null === $response) {
            $response = '';
        }

        return JsonResponse::fromJsonString($response);
    }
}
