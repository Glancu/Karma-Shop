<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopCategoryRepository;
use App\Serializer\ShopSerializeDataResponse;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
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
    private ShopCategoryRepository $shopCategoryRepository;
    private ShopSerializeDataResponse $shopSerializeDataResponse;

    /**
     * CategoryController constructor.
     *
     * @param ShopCategoryRepository $shopCategoryRepository
     * @param ShopSerializeDataResponse $shopSerializeDataResponse
     */
    public function __construct(
        ShopCategoryRepository $shopCategoryRepository,
        ShopSerializeDataResponse $shopSerializeDataResponse
    ) {
        $this->shopCategoryRepository = $shopCategoryRepository;
        $this->shopSerializeDataResponse = $shopSerializeDataResponse;
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
     */
    public function getShopCategoriesList(): JsonResponse
    {
        $items = $this->shopCategoryRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']
        );

        return JsonResponse::fromJsonString($this->shopSerializeDataResponse->getShopCategoriesList($items));
    }
}
