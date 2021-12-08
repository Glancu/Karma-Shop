<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ProductReview;
use App\Entity\ShopProduct;
use App\Form\Type\CreateProductReviewFormType;
use App\Service\RedisCacheService;
use App\Service\RequestService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

/**
 * Class ProductReviewController
 *
 * @package App\Controller
 *
 * @Route("/shop/product-review")
 *
 * @OA\Tag(name="Product")
 */
class ProductReviewController
{
    private FormFactoryInterface $form;
    private EntityManagerInterface $entityManager;
    private RedisCacheService $redisCacheService;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RedisCacheService $redisCacheService
    ) {
        $this->form = $formFactory;
        $this->entityManager = $entityManager;
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/create", name="app_shop_product_review_create", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to submit to the newsletter",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"name", "email", "message", "rating", "productUuid", "dataProcessingAgreement"},
     *             @OA\Property(
     *                 property="name",
     *                 description="name",
     *                 type="string",
     *                 example="John"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="email",
     *                 type="string",
     *                 example="user@email.com"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 description="message",
     *                 type="string",
     *                 example="This is simple message"
     *             ),
     *             @OA\Property(
     *                 property="rating",
     *                 description="rating",
     *                 type="integer",
     *                 example=4
     *             ),
     *             @OA\Property(
     *                 property="productUuid",
     *                 description="Uuid of product",
     *                 type="string",
     *                 example="e363025f-5c91-11eb-8a84-0242ac1fsdf"
     *             ),
     *             @OA\Property(
     *                 property="dataProcessingAgreement",
     *                 description="Accept data terms",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Product review was created",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "uuid": "e9f5df68-7ab7-441a-bfc7-058e77b6214e"}
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Email is not valid."}
     *     )
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param RequestService $requestService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public function createProductReview(Request $request, RequestService $requestService): JsonResponse
    {
        $em = $this->entityManager;
        $formService = $this->form;

        $requiredDataFromContent = [
            'name',
            'email',
            'message',
            'rating',
            'productUuid',
            'dataProcessingAgreement'
        ];

        $data = $requestService->validRequestContentAndGetData($request->getContent(), $requiredDataFromContent);
        if ($data instanceof JsonResponse) {
            return $data;
        }

        $form = $formService->create(CreateProductReviewFormType::class);
        $form->handleRequest($request);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!UserService::validateEmail($data['email'])) {
                $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

                return new JsonResponse($errorsList, 400);
            }

            $shopProduct = $this->redisCacheService->getAndSaveIfNotExist(
                'shop.product.'.$data['productUuid'],
                ShopProduct::class,
                'findActiveByUuid',
                $data['productUuid']
            );
            if (!$shopProduct) {
                return new JsonResponse(['error' => true, 'message' => 'Product was not found.'], 404);
            }

            $productReview = new ProductReview(
                $data['name'],
                $data['email'],
                (int)$data['rating'],
                $data['message']
            );

            $em->persist($productReview);

            $shopProduct->addReview($productReview);

            $em->persist($shopProduct);
            $em->flush();

            return new JsonResponse(['error' => false, 'uuid' => $productReview->getUuid()], 201);
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $form->getErrors(true)->count();
        if ($errorsCount > 0) {
            $errorsList['message'] = $form->getErrors(true)[0]->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
