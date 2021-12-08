<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ShopCategory;
use App\Service\RedisCacheService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 *
 * @package App\Controller\Shop
 *
 * @Route("/shop/categories")
 *
 * @OA\Tag(name="Shop")
 */
class CategoryController
{
    private RedisCacheService $redisCacheService;

    public function __construct(
        RedisCacheService $redisCacheService
    ) {
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/list", name="shop_categories_list", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of categories",
     *     @OA\JsonContent(
     *          type="string", example={{ "title": "Meat and Fish", "slug": "meat-and-fish", "enable": true, "uuid": "c0cb68af-bea3-43fe-b8eb-6a8a1c40268c", "countProducts": 6 }, { "title": "Fruits and Vegetables", "slug": "fruits-and-vegetables", "enable": true, "uuid": "6f77f136-1627-4fda-837b-cc5677eba9e2", "countProducts": 4 }},
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function getShopCategoriesList(): JsonResponse
    {
        $response = $this->redisCacheService->getAndSaveIfNotExistWithSerializeData(
            'shop.category.findAllEnable',
            ShopCategory::class,
            'findAllEnable',
            'shopSerializeDataResponse',
            'getShopCategoriesList'
        );

        return JsonResponse::fromJsonString($response);
    }
}
