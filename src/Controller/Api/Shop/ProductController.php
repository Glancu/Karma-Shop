<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Repository\ShopProductRepository;
use App\Serializer\ShopSerializer;
use App\Service\MoneyService;
use App\Service\SerializeDataResponse;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    private SerializeDataResponse $serializeDataResponse;
    private ShopProductRepository $shopProductRepository;
    private ShopSerializer $shopSerializer;

    /**
     * ProductController constructor.
     *
     * @param SerializeDataResponse $serializeDataResponse
     * @param ShopProductRepository $shopProductRepository
     */
    public function __construct(
        SerializeDataResponse $serializeDataResponse,
        ShopProductRepository $shopProductRepository,
        ShopSerializer $shopSerializer
    ) {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopProductRepository = $shopProductRepository;
        $this->shopSerializer = $shopSerializer;
    }

    /**
     * @Route("/list", name="app_shop_products_list", methods={"GET"})
     *
     * @param Request $request
     * @param MoneyService $moneyService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getProductsList(Request $request, MoneyService $moneyService): JsonResponse
    {
        $productRepository = $this->shopProductRepository;
        $serializer = $this->serializeDataResponse;

        $limit = $request->query->get('limit') ?: 12;
        $offset = $request->query->get('offset') ?: 0;
        $sortBy = $request->query->get('sortBy') ?: null;
        if ($sortBy === 'price') {
            $sortBy = 'priceNet';
        }
        $color = $request->query->get('color');
        $brand = $request->query->get('brand');
        $sortOrder = $request->query->get('sortOrder') ?: 'DESC';
        $priceFrom = $request->query->get('priceFrom') ?: null;
        if ($priceFrom) {
            $priceFrom = $moneyService->convertFloatToInt($priceFrom);
        }
        $priceTo = $request->query->get('priceTo') ?: null;
        if ($priceTo) {
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

        $countProducts = $productRepository->getCountProductsByParameters($parameters);
        if ($countProducts < 5) {
            $parameters['limit'] = 10;
            $parameters['offset'] = 0;
        }

        $products = $productRepository->getProductsWithLimitAndOffsetAndCountItems($parameters);

        $productData = $serializer->getProductsData($products, $countProducts);

        if ($countProducts === 0 && !$products) {
            $productData = json_encode(['errorMessage' => 'Products was not found.'], JSON_THROW_ON_ERROR);
        }

        return JsonResponse::fromJsonString($productData);
    }

    /**
     * @Route("/search/{name}", name="app_shop_product_search", methods={"GET"})
     *
     * @param string $name
     *
     * @return JsonResponse
     */
    public function getProductsByNameLikeAction(string $name): JsonResponse
    {
        $name = htmlspecialchars((string)$name, ENT_QUOTES);

        $products = $this->shopProductRepository->findByNameLike($name);
        $serializer = $this->serializeDataResponse->getProductsSearchData($products);

        return JsonResponse::fromJsonString($serializer);
    }

    /**
     * @Route("/product/{slug}", name="app_shop_product_show", methods={"GET"})
     *
     * @param $slug
     *
     * @return JsonResponse
     */
    public function getProductAction($slug): JsonResponse
    {
        $serializer = $this->serializeDataResponse;

        $product = $this->shopProductRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$product) {
            return new JsonResponse(['error' => true, 'message' => 'Product was not found.'], 404);
        }

        return new JsonResponse($serializer->getSingleProductData($product));
    }

    /**
     * @Route("/latest", name="app_shop_products_latest", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @throws JsonException
     */
    public function getLatestProducts(): JsonResponse
    {
        $productRepository = $this->shopProductRepository;
        $serializer = $this->serializeDataResponse;

        $countProducts = 8;

        $products = $productRepository->getLatestProducts();

        $productData = $serializer->getProductsData($products, $countProducts);

        if ($countProducts === 0 && !$products) {
            $productData = json_encode(['errorMessage' => 'Products was not found.'], JSON_THROW_ON_ERROR);
        }

        return JsonResponse::fromJsonString($productData);
    }
}
