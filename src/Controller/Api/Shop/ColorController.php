<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ShopColor;
use App\Service\RedisCacheService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ColorController
 *
 * @package App\Controller\Shop
 *
 * @Route("/shop/colors")
 *
 * @OA\Tag(name="Shop")
 */
class ColorController
{
    private RedisCacheService $redisCacheService;

    public function __construct(
        RedisCacheService $redisCacheService
    ) {
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/list", name="shop_colors_list", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of colors",
     *     @OA\JsonContent(
     *          type="string", example={{ "name": "Black with red", "slug": "black-with-red", "countProducts": 2 }, { "name": "Black Leather", "slug": "black-leather", "countProducts": 3 }},
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function getShopColorsList(): JsonResponse
    {
        $response = $this->redisCacheService->getAndSaveIfNotExistWithSerializeData(
            'shop.color.findAllEnable',
            ShopColor::class,
            'findAllEnable',
            'shopSerializeDataResponse',
            'getShopColorsList'
        );

        return JsonResponse::fromJsonString($response);
    }
}
