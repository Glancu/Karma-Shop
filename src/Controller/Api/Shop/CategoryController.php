<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopCategoryRepository;
use App\Serializer\SerializeDataResponse;
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
    private SerializeDataResponse $serializeDataResponse;
    private ShopCategoryRepository $shopCategoryRepository;

    /**
     * CategoryController constructor.
     *
     * @param SerializeDataResponse $serializeDataResponse
     * @param ShopCategoryRepository $shopCategoryRepository
     */
    public function __construct(
        SerializeDataResponse $serializeDataResponse,
        ShopCategoryRepository $shopCategoryRepository
    ) {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopCategoryRepository = $shopCategoryRepository;
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
        $serializer = $this->serializeDataResponse;

        $items = $this->shopCategoryRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']
        );

        return JsonResponse::fromJsonString($serializer->getShopCategoriesList($items));
    }
}
