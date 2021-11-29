<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\BlogPost;
use App\Entity\ShopProduct;
use App\Repository\BlogPostRepository;
use App\Repository\ShopProductRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 *
 * @package App\Controller\Api
 *
 *
 * @Route("/search")
 *
 * @OA\Tag(name="Search")
 */
class SearchController
{
    private ShopProductRepository $shopProductRepository;
    private BlogPostRepository $blogPostRepository;

    public function __construct(ShopProductRepository $shopProductRepository, BlogPostRepository $blogPostRepository)
    {
        $this->shopProductRepository = $shopProductRepository;
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @Route("/list", name="app_search_list", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="query",
     *     in="query",
     *     description="Search by name",
     *     required=true,
     *     example="enim"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Get shop product or blog post by name",
     *     @OA\JsonContent(
     *        type="string",
     *        example={"query": "enim", "suggestions": { { "value": "Repellat sit aperiam blanditiis enim delectus et.", "data": "Repellat sit aperiam blanditiis enim delectus et.", "slug": "repellat-sit-aperiam-blanditiis-enim-delectus-et", "type": "blog_post" } }}
     *     )
     * )
     *
     * @Security()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        // This API was created for https://github.com/devbridge/jQuery-Autocomplete

        if (!$request->get('query')) {
            return new JsonResponse([
                'query' => null,
                'suggestions' => []
            ]);
        }

        $query = htmlspecialchars($request->get('query'), ENT_QUOTES);

        $shopProducts = $this->shopProductRepository->findByNameLike($query);

        $blogPosts = $this->blogPostRepository->findByTitleLike($query);

        $suggestions = [];

        /**
         * @var ShopProduct $shopProduct
         */
        foreach ($shopProducts as $shopProduct) {
            $suggestions[] = [
                'value' => $shopProduct->getName(),
                'data' => $shopProduct->getName(),
                'slug' => $shopProduct->getSlug(),
                'type' => 'shop_product'
            ];
        }

        /**
         * @var BlogPost $blogPost
         */
        foreach ($blogPosts as $blogPost) {
            $suggestions[] = [
                'value' => $blogPost->getTitle(),
                'data' => $blogPost->getTitle(),
                'slug' => $blogPost->getSlug(),
                'type' => 'blog_post'
            ];
        }

        return new JsonResponse([
            'query' => $query,
            'suggestions' => $suggestions
        ]);
    }
}
