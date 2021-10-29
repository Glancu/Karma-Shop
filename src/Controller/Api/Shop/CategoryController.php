<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopCategoryRepository;
use App\Service\SerializeDataResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 *
 * @Route("/categories")
 *
 * @package App\Controller\Shop
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
    )
    {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopCategoryRepository = $shopCategoryRepository;
    }

    /**
     * @Route("/list", name="shop_categories_list", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getCategoriesList(): JsonResponse
    {
        $serializer = $this->serializeDataResponse;

        $items = $this->shopCategoryRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']);

        return JsonResponse::fromJsonString($serializer->getCategoriesList($items));
    }
}
