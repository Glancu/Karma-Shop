<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopColorRepository;
use App\Service\SerializeDataResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ColorController
 *
 * @Route("/colors")
 *
 * @package App\Controller\Shop
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
    )
    {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopColorRepository = $shopColorRepository;
    }

    /**
     * @Route("/list", name="shop_colors_list", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getCategoriesList(): JsonResponse
    {
        $serializer = $this->serializeDataResponse;

        $items = $this->shopColorRepository->findBy(
            ['enable' => true],
            ['id' => 'DESC']);

        return JsonResponse::fromJsonString($serializer->getColorsList($items));
    }
}
