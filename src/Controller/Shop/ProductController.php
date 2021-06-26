<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Service\MoneyService;
use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 *
 * @package App\Controller\Shop
 *
 * @Route("/api/products")
 */
class ProductController
{
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * ProductController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SerializeDataResponse $serializeDataResponse
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializeDataResponse $serializeDataResponse
    ) {
        $this->entityManager = $entityManager;
        $this->serializeDataResponse = $serializeDataResponse;
    }

    /**
     * @Route("/list", name="shop_products_list", methods={"GET"})
     *
     * @param Request $request
     * @param MoneyService $moneyService
     *
     * @return Response
     *
     * @throws JsonException
     */
    public function getProductsList(Request $request, MoneyService $moneyService): Response
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;

        $limit = $request->query->get('limit') ?: 12;
        $offset = $request->query->get('offset') ?: 0;
        $sortBy = $request->query->get('sortBy') ?: null;
        if($sortBy === 'price') {
            $sortBy = 'priceNet';
        }
        $color = $request->query->get('color');
        $brand = $request->query->get('brand');
        $sortOrder = $request->query->get('sortOrder') ?: 'DESC';
        $priceFrom = $request->query->get('priceFrom') ?: null;
        if($priceFrom) {
            $priceFrom = $moneyService->convertFloatToInt($priceFrom);
        }
        $priceTo = $request->query->get('priceTo') ?: null;
        if($priceTo) {
            $priceTo = $moneyService->convertFloatToInt($priceTo);
        }

        $parameters = [
            'limit' => $limit,
            'offset' => $offset,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'brand' => $brand,
            'color' => $color,
            'priceFrom' => $priceFrom,
            'priceTo' => $priceTo,
            'categorySlug' => $request->query->get('categorySlug')
        ];

        $countProducts = $em->getRepository('App:ShopProduct')->getCountProductsByParameters($parameters);
        if($countProducts < 5) {
            $parameters['limit'] = 10;
            $parameters['offset'] = 0;
        }

        $products = $em->getRepository('App:ShopProduct')
                   ->getProductsWithLimitAndOffsetAndCountItems($parameters);

        $productData = $serializer->getProductsData($products, $countProducts);

        if($countProducts === 0 && !$products) {
            $productData = json_encode(['errorMessage' => 'Products was not found.'], JSON_THROW_ON_ERROR);
        }

        $response = new Response($productData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
