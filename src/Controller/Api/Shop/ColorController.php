<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopColorRepository;
use App\Serializer\ShopSerializeDataResponse;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
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
    private ShopColorRepository $shopColorRepository;
    private ShopSerializeDataResponse $shopSerializeDataResponse;

    /**
     * ColorController constructor.
     *
     * @param ShopColorRepository $shopColorRepository
     * @param ShopSerializeDataResponse $shopSerializeDataResponse
     */
    public function __construct(
        ShopColorRepository $shopColorRepository,
        ShopSerializeDataResponse $shopSerializeDataResponse
    ) {
        $this->shopColorRepository = $shopColorRepository;
        $this->shopSerializeDataResponse = $shopSerializeDataResponse;
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
     */
    public function getShopColorsList(): JsonResponse
    {
        $items = $this->shopColorRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']
        );

        return JsonResponse::fromJsonString($this->shopSerializeDataResponse->getShopColorsList($items));
    }
}
