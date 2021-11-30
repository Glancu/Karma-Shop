<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ShopProduct;
use App\Repository\ShopProductRepository;
use App\Serializer\ShopNormalizer;
use App\Service\MoneyService;
use App\Service\RedisCacheService;
use App\Serializer\SerializeDataResponse;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 *
 * @package App\Controller\Shop
 *
 * @Route("/shop/products")
 *
 * @OA\Tag(name="Product")
 */
class ProductController
{
    private SerializeDataResponse $serializeDataResponse;
    private ShopProductRepository $shopProductRepository;
    private ShopNormalizer $shopSerializer;
    private RedisCacheService $redisCacheService;

    /**
     * ProductController constructor.
     *
     * @param SerializeDataResponse $serializeDataResponse
     * @param ShopProductRepository $shopProductRepository
     */
    public function __construct(
        SerializeDataResponse $serializeDataResponse,
        ShopProductRepository $shopProductRepository,
        ShopNormalizer $shopSerializer,
        RedisCacheService $redisCacheService
    ) {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->shopProductRepository = $shopProductRepository;
        $this->shopSerializer = $shopSerializer;
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/list", name="app_shop_products_list", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Limit of products (default 12)",
     *     required=false,
     *     @OA\Schema(type="integer", example=12)
     * )
     * @OA\Parameter(
     *     name="offset",
     *     in="query",
     *     description="Offset of products list (default 0)",
     *     required=false,
     *     @OA\Schema(type="integer", example=0)
     * )
     * @OA\Parameter(
     *     name="color",
     *     in="query",
     *     description="Search products by color slug",
     *     required=false,
     *     @OA\Schema(type="string", example="black")
     * )
     * @OA\Parameter(
     *     name="brand",
     *     in="query",
     *     description="Search products by brand slug",
     *     required=false,
     *     @OA\Schema(type="string", example="apple")
     * )
     * @OA\Parameter(
     *     name="categorySlug",
     *     in="query",
     *     description="Search products by category slug",
     *     required=false,
     *     @OA\Schema(type="string", example="meat-and-fish")
     * )
     * @OA\Parameter(
     *     name="sortOrder",
     *     in="query",
     *     description="You can change order of products list",
     *     required=false,
     *     @OA\Schema(type="string", enum={"DESC", "ASC"})
     * )
     * @OA\Parameter(
     *     name="priceFrom",
     *     in="query",
     *     description="Show products with price above",
     *     required=false,
     *     @OA\Schema(type="string", example="100")
     * )
     * @OA\Parameter(
     *     name="priceTo",
     *     in="query",
     *     description="Show products with maximum price",
     *     required=false,
     *     @OA\Schema(type="string", example="200")
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="List of products with count items",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "countItems": 1, "items": { { "name": "product 7", "slug": "product-7", "quantity": 23, "description": "<p>this is simple description for product 7</p>", "shopBrand": { "title": "Apple", "slug": "apple", "enable": true, "uuid": "891e2b3b-3b04-4f29-9574-d051358aab7e" }, "shopCategory": { "title": "Fruits and Vegetables", "slug": "fruits-and-vegetables", "enable": true, "uuid": "6f77f136-1627-4fda-837b-cc5677eba9e2" }, "shopProductSpecifications": { { "value": "50", "uuid": "a0438dc4-faea-47eb-bf0c-a3a430fdad8d", "name": "Height" } }, "reviews": {}, "shopColors": { { "name": "Black with red", "slug": "black-with-red", "enable": true, "uuid": "4a3ddf65-35c4-41b7-af9e-8fffc1a50136" } }, "enable": true, "uuid": "c0b8ed63-b795-408c-b720-f7a2ec4c5706", "priceNet": "70.00", "priceGross": "74.00", "images": { { "name": "p7.jpg", "url": "https://website.com/p7.jpg" }, { "name": "p6.jpg", "url": "https://website.com/p6.jpg" } } } } }}
     *     )
     * )
     *
     * @Security()
     *
     * @param Request $request
     * @param MoneyService $moneyService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public function getProductsList(Request $request, MoneyService $moneyService): JsonResponse
    {
        $serializer = $this->serializeDataResponse;
        $redisCacheService = $this->redisCacheService;

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
            'categorySlug' => $request->query->get('category')
        ];

        $countProducts = $redisCacheService->getAndSaveIfNotExist(
            'product_getProductsList__countItems',
            ShopProduct::class,
            'getCountProductsByParameters',
            $parameters
        );
        if ($countProducts < 5) {
            $parameters['limit'] = 10;
            $parameters['offset'] = 0;
        } elseif($countProducts / 2 <= $parameters['limit']) {
            $parameters['limit'] = $countProducts;
        }

        $products = $this->shopProductRepository->getProductsWithLimitAndOffsetAndCountItems($parameters);

        $productData = $serializer->getShopProductsData($products, (int)$countProducts, $parameters);

        if ($countProducts === 0 && !$products) {
            $productData = json_encode(['errorMessage' => 'Products was not found.'], JSON_THROW_ON_ERROR);
        }

        return JsonResponse::fromJsonString($productData);
    }

    /**
     * @Route("/product/{slug}", name="app_shop_product_show", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     description="Slug of product",
     *     required=true,
     *     example="product"
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Find product by product slug",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "name": "product1", "slug": "product1", "quantity": 100, "description": "<p>qwert</p>", "shopBrand": { "title": "Apple", "slug": "apple", "enable": true, "uuid": "891e2b3b-3b04-4f29-9574-d051358aab7e" }, "shopCategory": { "title": "Fruits and Vegetables", "slug": "fruits-and-vegetables", "enable": true, "uuid": "6f77f136-1627-4fda-837b-cc5677eba9e2" }, "shopProductSpecifications": { { "value": "20", "uuid": "3abb3164-ffffW331d-11eb-b126-0242ac130004", "name": "Width" }, { "value": "19", "uuid": "3abb34f1111d-331d-11eb-b126-0242ac130004", "name": "Height" } }, "reviews": {}, "shopColors": {}, "comments": {}, "enable": true, "uuid": "e363025f-5c91-11eb-8a84-0242ac1fsdf", "priceNet": "0.11", "priceGross": "0.13", "images": { { "name": "image1.jpg", "url": "https://website.com/image1.jpg" }, { "name": "image2.jpg", "url": "https://website.com/image2.jpg" } } }}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Product was not found."}
     *     )
     * )
     *
     * @Security()
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

        return new JsonResponse($serializer->getSingleShopProductData($product));
    }

    /**
     * @Route("/latest", name="app_shop_products_latest", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of latest products with count items",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "countItems": 1, "items": { { "name": "product 7", "slug": "product-7", "quantity": 23, "description": "<p>this is simple description for product 7</p>", "shopBrand": { "title": "Apple", "slug": "apple", "enable": true, "uuid": "891e2b3b-3b04-4f29-9574-d051358aab7e" }, "shopCategory": { "title": "Fruits and Vegetables", "slug": "fruits-and-vegetables", "enable": true, "uuid": "6f77f136-1627-4fda-837b-cc5677eba9e2" }, "shopProductSpecifications": { { "value": "50", "uuid": "a0438dc4-faea-47eb-bf0c-a3a430fdad8d", "name": "Height" } }, "reviews": {}, "shopColors": { { "name": "Black with red", "slug": "black-with-red", "enable": true, "uuid": "4a3ddf65-35c4-41b7-af9e-8fffc1a50136" } }, "enable": true, "uuid": "c0b8ed63-b795-408c-b720-f7a2ec4c5706", "priceNet": "70.00", "priceGross": "74.00", "images": { { "name": "image1.jpg", "url": "https://website.com/image1.jpg" }, { "name": "image2.jpg", "url": "https://website.com/image2.jpg" } } } } }}
     *     )
     * )
     *
     * @Security()
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

        $productData = $serializer->getShopProductsData($products, $countProducts);

        if ($countProducts === 0 && !$products) {
            $productData = json_encode(['errorMessage' => 'Products was not found.'], JSON_THROW_ON_ERROR);
        }

        return JsonResponse::fromJsonString($productData);
    }
}
