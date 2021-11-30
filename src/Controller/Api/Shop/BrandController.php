<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopBrandRepository;
use App\Serializer\ShopSerializeDataResponse;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
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
    private ShopBrandRepository $shopBrandRepository;
    private ShopSerializeDataResponse $shopSerializeDataResponse;

    /**
     * BrandController constructor.
     *
     * @param ShopBrandRepository $shopBrandRepository
     * @param ShopSerializeDataResponse $shopSerializeDataResponse
     */
    public function __construct(
        ShopBrandRepository $shopBrandRepository,
        ShopSerializeDataResponse $shopSerializeDataResponse
    ) {
        $this->shopBrandRepository = $shopBrandRepository;
        $this->shopSerializeDataResponse = $shopSerializeDataResponse;
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
     */
    public function getShopBrandsList(): JsonResponse
    {
        $items = $this->shopBrandRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']
        );

        return JsonResponse::fromJsonString($this->shopSerializeDataResponse->getShopBrandsList($items));
    }
}
