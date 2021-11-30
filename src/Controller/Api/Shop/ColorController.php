<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopColorRepository;
use App\Serializer\SerializeDataResponse;
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
    private SerializeDataResponse $serializeDataResponse;
    private ShopColorRepository $shopColorRepository;

    /**
     * ColorController constructor.
     *
     * @param SerializeDataResponse $serializeDataResponse
     * @param ShopColorRepository $shopColorRepository
     */
    public function __construct(
        SerializeDataResponse $serializeDataResponse,
        ShopColorRepository $shopColorRepository
    ) {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopColorRepository = $shopColorRepository;
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
        $serializer = $this->serializeDataResponse;

        $items = $this->shopColorRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']
        );

        return JsonResponse::fromJsonString($serializer->getShopColorsList($items));
    }
}
