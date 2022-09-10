<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Entity\BlogTag;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use App\Serializer\BlogSerializeDataResponse;
use App\Serializer\SerializeDataResponse;
use App\Service\ImageService;
use App\Service\RedisCacheService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class BlogController
 *
 * @package App\Controller\Api
 *
 * @Route("/blog")
 *
 * @OA\Tag(name="Blog")
 */
class BlogController
{
    private SerializeDataResponse $serializeDataResponse;
    private BlogPostRepository $blogPostRepository;
    private RouterInterface $router;
    private BlogCategoryRepository $blogCategoryRepository;
    private RedisCacheService $redisCacheService;
    private BlogSerializeDataResponse $blogSerializeDataResponse;

    public function __construct(
        SerializeDataResponse $serializeDataResponse,
        BlogPostRepository $blogPostRepository,
        RouterInterface $router,
        BlogCategoryRepository $blogCategoryRepository,
        RedisCacheService $redisCacheService,
        BlogSerializeDataResponse $blogSerializeDataResponse
    ) {
        $this->serializeDataResponse = $serializeDataResponse;
        $this->blogPostRepository = $blogPostRepository;
        $this->router = $router;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->redisCacheService = $redisCacheService;
        $this->blogSerializeDataResponse = $blogSerializeDataResponse;
    }

    /**
     * @Route("/posts/list", name="app_blog_posts_list", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Limit of posts (default 12)",
     *     required=false,
     *     @OA\Schema(type="integer", example=12)
     * )
     * @OA\Parameter(
     *     name="offset",
     *     in="query",
     *     description="Offset of posts list (default 0)",
     *     required=false,
     *     @OA\Schema(type="integer", example=0)
     * )
     * @OA\Parameter(
     *     name="category",
     *     in="query",
     *     description="Search post by category slug",
     *     required=false,
     *     @OA\Schema(type="string", example="technology")
     * )
     * @OA\Parameter(
     *     name="tag",
     *     in="query",
     *     description="Search post by tag slug",
     *     required=false,
     *     @OA\Schema(type="string", example="technology")
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="List of posts with count items",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "countItems": 41, "items": { { "title": "Et at laudantium neque aut nihil nesciunt.", "slug": "et-at-laudantium-neque-aut-nihil-nesciunt", "shortContent": "Officia veritatis assumenda recusandae voluptas nihil voluptatum. Odio veritatis mollitia iste ea provident. Quod voluptas corporis quo dolores consequuntur eius. Voluptas sit aut ab. Autem mollitia dignissimos voluptas sunt nesciunt voluptas ratione. Eum consequatur iure sed quam dolorem. A corrupti voluptatem nulla cum recusandae quasi. Explicabo aut recusandae optio repellat. Illum blanditiis laudantium numquam ullam sint est ducimus. Accusantium excepturi excepturi optio facere fugit sint.", "views": 3214, "category": { "name": "Fashion", "slug": "fashion", "uuid": "10c4c592-b890-40d5-b7b1-4dbc742cc145" }, "image": { "name": "blog_image.jpg", "url": "http://local.karma.pl/uploads/media/default/0001/01/thumb_558_default_big.jpeg" }, "tags": { { "name": "Technology", "slug": "technology", "uuid": "157fe00d-e358-4b98-b067-cb5d873d1231" }, { "name": "Art", "slug": "art", "uuid": "dc081d21-7249-4f0f-8d0a-5ec7cf6f7dc1" } }, "uuid": "c9f56552-6a18-49ce-b851-48abcfc303fd", "enable": true, "commentsCount": 3, "createdAt": "19-11-2021" } } }}
     *     )
     * )
     *
     * @Security()
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function getBlogPostsList(Request $request): JsonResponse
    {
        $blogPostRepository = $this->blogPostRepository;

        $defaultLimit = 12;

        $limit = $request->query->get('limit') ?: $defaultLimit;
        $offset = $request->query->get('offset') ?: 0;

        $parameters = [
            'limit' => $limit,
            'offset' => $offset,
            'category' => $request->query->get('category'),
            'tag' => $request->query->get('tag')
        ];

        $countPosts = $blogPostRepository->getCountPostsByParameters($parameters);
        if ($countPosts < 5) {
            $parameters['limit'] = $defaultLimit;
            $parameters['offset'] = 0;
        }

        if ((int)ceil($countPosts / $defaultLimit) === 2) {
            $parameters['limit'] = $countPosts;
        }

        $postsData = $this->redisCacheService->getAndSaveIfNotExistWithSerializeData(
            'blog.getBlogPostsList',
            BlogPost::class,
            'getPostsWithLimitAndOffsetAndCountItems',
            'blogSerializeDataResponse',
            'getBlogPostsData',
            $parameters,
            [$countPosts, $parameters]
        );

        if ($countPosts === 0 || !$postsData) {
            $postsData = json_encode(['errorMessage' => 'Posts was not found.'], JSON_THROW_ON_ERROR);
        }

        return JsonResponse::fromJsonString($postsData);
    }

    /**
     * @Route("/post/{slug}", name="app_blog_post_show", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     description="Slug of post",
     *     required=true,
     *     example="cupiditate-facere-est-voluptatem-atque-voluptate"
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Find post by post slug",
     *     @OA\JsonContent(
     *        type="string",
     *        example={ "title": "Et at laudantium neque aut nihil nesciunt.", "slug": "et-at-laudantium-neque-aut-nihil-nesciunt", "shortContent": "Officia veritatis assumenda recusandae voluptas nihil voluptatum. Odio veritatis mollitia iste ea provident. Quod voluptas corporis quo dolores consequuntur eius. Voluptas sit aut ab. Autem mollitia dignissimos voluptas sunt nesciunt voluptas ratione. Eum consequatur iure sed quam dolorem. A corrupti voluptatem nulla cum recusandae quasi. Explicabo aut recusandae optio repellat. Illum blanditiis laudantium numquam ullam sint est ducimus. Accusantium excepturi excepturi optio facere fugit sint.", "longContent": "Et dolorem totam culpa ut vel laborum. A voluptate quia sit qui. Quas accusamus consequatur aperiam voluptatibus rem rerum. Quia omnis ipsa quia voluptate. Dolor voluptas laudantium velit sit. Maiores libero aut eum consequatur voluptas. Enim qui veritatis ea neque accusamus nostrum. Dolor officiis perferendis nesciunt optio quibusdam unde error. Et in earum quia. Provident reiciendis nisi facere. Voluptate aperiam quas nam ea iste id vero eos. Quas culpa qui aut hic labore rerum. Quod nisi sequi pariatur explicabo debitis atque repudiandae. Rerum dolores rerum voluptatibus libero pariatur voluptas. Aliquam qui enim omnis eaque corrupti. Aliquid architecto nihil minima quis cupiditate. Voluptas consequuntur accusantium adipisci et quos quas. Et est voluptas qui minima eveniet commodi. Consequatur expedita esse non quam vitae nobis. Reprehenderit animi facilis sed ut. Saepe dignissimos ab corporis eos dolor qui. Eos odit velit quo in nam at dolorum a. Et iste aperiam et nemo. Ex cupiditate qui explicabo consequuntur est nobis neque dolor. Eum omnis repellendus non omnis esse ad libero earum. Tempore mollitia amet sequi in. Aliquam aut porro eum explicabo. Dolorum distinctio quis sint laboriosam harum aperiam id. Aut totam doloremque natus. Enim esse deserunt ipsam consequatur qui ut magni. Ducimus totam soluta voluptates et ex. Aut natus iusto voluptas eos iure aut consequatur. Quod porro quis dolorum sit. Vel quae minus deserunt cupiditate aperiam totam. Voluptatum corporis suscipit fugit quae necessitatibus. Nostrum eligendi quibusdam tenetur. Dicta culpa autem ut doloribus saepe. Ut consequuntur voluptatem maxime ut harum est. Tempore qui sunt et deleniti. Nam culpa et tenetur ut assumenda. Est fuga quibusdam totam natus illo ratione et voluptatem. Nisi quis quo praesentium et est repellendus eius. Mollitia accusantium est dolorem et a. Illum a dicta neque cupiditate quos. Ut est praesentium eos autem sequi. Nam sed harum dignissimos magni placeat ut quis. Minus repudiandae nesciunt quis quidem voluptas tempora. Ut error aut atque ad in dolor eveniet voluptate. Dolorem quaerat molestiae voluptatem repellat qui laborum. Impedit maiores consequuntur eaque sapiente corrupti excepturi a. Est voluptatem et aut quo. Molestias sint et cum sit quo eligendi quia. Laborum nobis similique quos voluptate dicta soluta natus. Id incidunt consequatur dolor et. Aut est delectus ea maiores dicta distinctio. Aut doloribus blanditiis exercitationem nemo. Soluta quibusdam et tempora modi odit temporibus. Placeat sit atque excepturi autem et similique. Velit qui similique animi harum deleniti omnis et. Repudiandae ad deleniti excepturi porro. Illum odit repudiandae sed illo quia odio. Dolor ut vel impedit voluptas aut. Libero aspernatur minus sequi tenetur aspernatur consequatur magni. Sapiente aliquam soluta ut aut saepe veniam. Aut odio eum incidunt ut ea sit quis. Nesciunt ducimus expedita quaerat reprehenderit at id ea. Cupiditate illo vitae voluptas et at. Quis nisi in repudiandae molestiae eos recusandae. Repellat error recusandae libero nulla esse pariatur magnam. Est animi voluptate delectus quos. Officiis in explicabo consequatur sunt. Delectus eum assumenda dolorum. Repellat non error dolorum consequatur fuga eius. Cum tenetur modi vitae modi ad quidem. Saepe id sit quam hic itaque quia. Voluptas sint ea consequatur ratione. Maxime culpa ut alias distinctio sit. Quasi unde consequuntur aut quod soluta. Et sed incidunt blanditiis qui totam voluptates dolores. Quo quas ratione nisi ullam. Nobis autem perferendis repellendus hic. Sunt facilis quia officia illo saepe architecto minima. Omnis natus qui quasi voluptatem incidunt voluptas odio. Ab totam ut deserunt. Voluptatem quia numquam voluptatibus et. Dolorem odit voluptatem facere quia laboriosam. Enim vero enim aut modi mollitia ea debitis. Officiis libero ipsam cum. Temporibus sit pariatur cupiditate eos. Quae fugit id laudantium consequatur aut quia itaque atque. Deserunt veniam explicabo rerum et. Fuga voluptate velit nobis exercitationem. Eligendi id eveniet dolorum. Doloribus enim totam tempora sed. Id quaerat aut deserunt est voluptatem. Harum ab exercitationem cupiditate vel incidunt nobis. Dolorem commodi facere soluta quo quidem inventore. Accusantium occaecati et eaque repellat maiores omnis. Sed reiciendis recusandae libero rerum nostrum ut ut. Sit exercitationem est sint accusamus autem et temporibus. Eos modi reiciendis aut dolor quis expedita similique. Ut rerum aliquid rerum ab et. Quidem vel provident veniam qui non. Aut ea reprehenderit nemo ut unde aliquid ipsa quo. Sunt tempora quas ut. Veritatis dolore unde et maxime. Aliquam perspiciatis rem similique iure sunt. Vero eius porro voluptates maxime numquam et. Incidunt rerum dolor non doloremque expedita modi. Quasi expedita et et harum odio eligendi perspiciatis. Odio quisquam similique fugit aut. Officia placeat animi ut similique in veritatis voluptatem vel. In soluta voluptate voluptas omnis earum unde exercitationem voluptates. Consequatur dolorem eos consequatur quas placeat quas corrupti. Expedita et et rem id. Beatae labore officia quae enim amet in. Laboriosam voluptas nostrum quia ut fuga. Nulla beatae consequuntur placeat iste. Provident autem in ipsum perspiciatis sed quas quibusdam. Facere ut hic cumque est nemo nisi commodi quia. Voluptatem consectetur quia et quam voluptate nobis. Non natus deleniti ullam optio sunt rem. Distinctio nulla commodi et. Quasi rerum id ipsum autem esse quasi magni. Consequuntur quibusdam quas eum quo et illum. Praesentium numquam corporis cum nemo doloribus. Voluptatibus a quia laborum. Quos ab quia corrupti a consectetur ex. Qui cumque aut voluptas fuga qui. Eveniet et sunt optio iure dolorem eos. Adipisci similique sint est molestias illum autem. Et porro adipisci natus distinctio accusantium ut culpa. Sit similique et voluptas illum similique. Sit eligendi at soluta voluptatem mollitia quae. Velit placeat aut dicta aut quibusdam. Quisquam eaque debitis corporis. In voluptatem aut optio minus illo sunt quis. Sint rerum harum officia architecto expedita. Doloremque et velit provident omnis. Vel est ea sunt aut perspiciatis rerum. At aut praesentium exercitationem qui corporis. Delectus ea occaecati voluptas quia aut. Perspiciatis assumenda ut hic quibusdam. Vitae reprehenderit ullam facere voluptates aut doloribus a. Dolor voluptates nihil rerum consequuntur eum. Aliquid illum quas ipsa tempore sed dolor totam repudiandae. Et incidunt adipisci tempora porro omnis. Omnis sit distinctio odio et vitae est. Sit at sit vero reiciendis officia. Rerum blanditiis consequuntur sequi sed. Repudiandae saepe mollitia velit consequuntur. Enim dolore consequatur adipisci molestias. Omnis eligendi ut eos magni nam et perspiciatis. Odit quam neque porro quod suscipit vero a.", "views": 3215, "category": { "name": "Fashion", "slug": "fashion", "uuid": "10c4c592-b890-40d5-b7b1-4dbc742cc145" }, "image": { "name": "blog_image.jpg", "url": "http://local.karma.pl/uploads/media/default/0001/01/thumb_558_default_big.jpeg" }, "comments": { { "name": "Tenetur.", "text": "Quia quibusdam maiores autem incidunt amet unde. Totam voluptatum a illum molestiae aut fugiat. Nostrum aut quo officia tenetur architecto sunt. Rerum ex sit omnis qui quos recusandae et aspernatur. Provident est ullam eos. Qui est suscipit vel. Omnis ex laborum voluptatem tempore. Nam at voluptatem quis natus. Corrupti ut vero mollitia qui mollitia. Rerum ipsum veniam totam hic nisi quo fuga. Qui in quis quo. Qui eum dolorem ea.", "enable": true, "uuid": "788b809d-cb23-4996-82d2-ea779682cf70", "createdAt": "19-11-2021 12:20:44" }, { "name": "Ea.", "text": "Repudiandae accusantium ut maiores atque dolores. Sequi rerum debitis alias ullam maxime. Reiciendis veritatis quia veniam perferendis sed corrupti quod. Ea deleniti nostrum quas iste qui quaerat sit nulla. Voluptatibus nostrum quasi eos. Libero sunt eos tenetur impedit consectetur nemo exercitationem. Voluptas magnam aut ex voluptatem aut.", "enable": true, "uuid": "97950f80-6dae-4eed-8536-646337c6aa9b", "createdAt": "19-11-2021 12:20:44" }, { "name": "Eum.", "text": "In ut est quos quas sed. Fugiat aliquid dolores quibusdam hic quia cupiditate. Fuga porro similique aliquid voluptate porro quidem mollitia id. Nulla dolorum harum facere possimus. Corrupti aut non culpa asperiores eum odio. Explicabo natus incidunt quo. Eveniet est animi asperiores ut placeat. Sunt natus optio voluptatem ipsum dolor. Dolores autem voluptatibus minus perspiciatis. Voluptas possimus aut aliquam alias distinctio debitis sit. Natus est cumque eum est.", "enable": true, "uuid": "de87ee9c-99e6-40a3-930c-af5d0f3459be", "createdAt": "19-11-2021 12:20:44" } }, "tags": { { "name": "Technology", "slug": "technology", "uuid": "157fe00d-e358-4b98-b067-cb5d873d1231" }, { "name": "Art", "slug": "art", "uuid": "dc081d21-7249-4f0f-8d0a-5ec7cf6f7dc1" } }, "uuid": "c9f56552-6a18-49ce-b851-48abcfc303fd", "enable": true, "createdAt": "19-11-2021", "previousPost": { "uuid": "376b133a-a671-483f-8f9f-76e6f74dabc7", "title": "Ipsam numquam quo temporibus.", "slug": "ipsam-numquam-quo-temporibus", "imageUrl": "http://local.karma.pl/uploads/media/default/0001/01/thumb_557_default_big.jpeg" }, "nextPost": { "uuid": "c5493bc9-5abe-49bb-9ed8-dd3fd19a9ff2", "title": "title", "slug": "title", "imageUrl": "http://local.karma.pl/uploads/media/default/0001/01/thumb_519_default_big.jpeg" } }
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Post was not found."}
     *     )
     * )
     *
     * @Security()
     *
     * @param ImageService $imageService
     * @param string $slug
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getSinglePost(ImageService $imageService, string $slug): JsonResponse
    {
        $post = $this->redisCacheService->getAndSaveIfNotExist(
            'blog.post.'.$slug,
            BlogPost::class,
            'findBySlug',
            $slug
        );
        if (!$post || !$post instanceof BlogPost) {
            return new JsonResponse(['error' => true, 'message' => 'Post was not found.'], 404);
        }

        $post->setViews($post->getViews() + 1);

        $this->blogPostRepository->update($post);

        $nextAndPreviousPostsIds = $this->blogPostRepository->getNextAndPreviousPostsIsById($post->getId());

        $data = $this->blogSerializeDataResponse->getSingleBlogPostData($post);

        if (isset($nextAndPreviousPostsIds['previous']) && $nextAndPreviousPostsIds['previous']) {
            $previousPost = $this->blogPostRepository->find($nextAndPreviousPostsIds['previous']);
            if ($previousPost && $previousPost->isEnable()) {
                $data['previousPost'] = [
                    'uuid' => $previousPost->getUuid(),
                    'title' => $previousPost->getTitle(),
                    'slug' => $previousPost->getSlug(),
                    'imageUrl' => $imageService->getImageNameAndUrl($previousPost->getImage())['url']
                ];
            }
        }

        if (isset($nextAndPreviousPostsIds['next']) && $nextAndPreviousPostsIds['next']) {
            $nextPost = $this->blogPostRepository->find($nextAndPreviousPostsIds['next']);
            if ($nextPost && $nextPost->isEnable()) {
                $data['nextPost'] = [
                    'uuid' => $nextPost->getUuid(),
                    'title' => $nextPost->getTitle(),
                    'slug' => $nextPost->getSlug(),
                    'imageUrl' => $imageService->getImageNameAndUrl($nextPost->getImage())['url']
                ];
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/posts/search-by-title", name="app_blog_posts_search_by_title", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function searchByTitle(Request $request): JsonResponse
    {
        // This API was created for https://github.com/devbridge/jQuery-Autocomplete

        if (!$request->get('query')) {
            return new JsonResponse([
                'query' => null,
                'suggestions' => []
            ]);
        }

        $query = htmlspecialchars($request->get('query'), ENT_QUOTES);

        $items = $this->redisCacheService->getAndSaveIfNotExist('blog.searchByTitle__'.$query, BlogPost::class, 'findByTitleLike', $query);

        $suggestions = [];

        /**
         * @var BlogPost $item
         */
        foreach ($items as $item) {
            $suggestions[] = [
                'value' => $item->getTitle(),
                'data' => $item->getTitle(),
                'slug' => $item->getSlug()
            ];
        }

        return new JsonResponse([
            'query' => $query,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * @Route("/posts/popular", name="app_blog_posts_popular", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of 4 popular posts for blog sidebar",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "title": "Voluptatibus reiciendis sit animi sint suscipit.", "slug": "voluptatibus-reiciendis-sit-animi-sint-suscipit", "views": 7974, "image": { "name": "blog_image.jpg", "url": "http://local.karma.pl/uploads/media/default/0001/01/thumb_535_default_big.jpeg" }, "uuid": "613f02dc-c221-4a21-9312-2a2141bb05c9", "enable": true }, { "title": "Amet nisi soluta autem ut eveniet est.", "slug": "amet-nisi-soluta-autem-ut-eveniet-est", "views": 7881, "image": { "name": "blog_image.jpg", "url": "http://local.karma.pl/uploads/media/default/0001/01/thumb_547_default_big.jpeg" }, "uuid": "79b1d582-f5dc-4771-b665-803eb2d4d4b5", "enable": true }}
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     * @throws InvalidArgumentException
     */
    public function getPopularPosts(): JsonResponse
    {
        $items = $this->redisCacheService->getAndSaveIfNotExist(
            'blog.getPopularPosts',
            BlogPost::class,
            'getPopularPosts'
        );
        if(null === $items || !is_array($items)) {
            $items = [];
        }

        $data = $this->blogSerializeDataResponse->getBlogPopularPosts($items);

        return JsonResponse::fromJsonString($data);
    }

    /**
     * @Route("/categories/latest", name="app_blog_categories_latest", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of 3 lastest categories",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "name": "Adventure", "slug": "adventure", "uuid": "0dd60ae7-7f84-493d-ba22-98b61c1430ae" }, { "name": "Architecture", "slug": "architecture", "uuid": "716288cc-0f55-4a54-8237-cdb6fa69cc69" }, { "name": "Food", "slug": "food", "uuid": "ecce9160-a2df-4380-a12a-8c9f1012e339" }}
     *     )
     * )
     *
     * @Security()
     *
     * @param int $limit
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     * @throws InvalidArgumentException
     */
    public function getLatestCategories(int $limit = 3): JsonResponse
    {
        $categories = $this->redisCacheService->getAndSaveIfNotExist('blog.getLatestCategories', BlogCategory::class, 'getItemsByLimit', $limit);
        if(null === $categories || !is_array($categories)) {
            $categories = [];
        }

        $data = $this->blogSerializeDataResponse->getBlogCategoriesLatestData($categories);

        return JsonResponse::fromJsonString($data);
    }

    /**
     * @Route("/categories/list", name="app_blog_categories_list", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of categories",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "name": "Lifestyle", "slug": "lifestyle", "uuid": "ae828194-5605-4705-acbd-a670c8291996", "countPosts": "6" }, { "name": "Adventure", "slug": "adventure", "uuid": "0dd60ae7-7f84-493d-ba22-98b61c1430ae", "countPosts": "4" }}
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function getCategoriesList(): JsonResponse
    {
        $categories = $this->redisCacheService->getAndSaveIfNotExist('blog_getCategoriesList', BlogCategory::class, 'getNamesWithCount');

        return new JsonResponse($categories);
    }

    /**
     * @Route("/tags/list", name="app_blog_tagslist", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of tags",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "name": "Technology", "slug": "technology", "uuid": "157fe00d-e358-4b98-b067-cb5d873d1231" }, { "name": "Lifestyle", "slug": "lifestyle", "uuid": "15738dfc-21fd-4204-927f-ecf17d0040d7" }}
     *     )
     * )
     *
     * @Security()
     *
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function getTagsList(): JsonResponse
    {
        $tags = $this->redisCacheService->getAndSaveIfNotExist('blog_getTagsList', BlogTag::class, 'getNamesList');

        return new JsonResponse($tags);
    }
}
