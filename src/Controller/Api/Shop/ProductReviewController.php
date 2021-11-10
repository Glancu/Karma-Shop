<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ProductReview;
use App\Entity\ShopProduct;
use App\Form\Type\CreateProductReviewFormType;
use App\Service\RequestService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * OrderController constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->form = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/create", name="app_shop_product_review_create", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to submit to the newsletter",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
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
     *                 property="phoneNumber",
     *                 description="Phone number",
     *                 type="string",
     *                 example="000-000-000"
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
     *
     * @return JsonResponse
     */
    public function createProductReview(Request $request): JsonResponse
    {
        $em = $this->entityManager;
        $formService = $this->form;

        $data = $this->getDataFromRequestToCreateProductReview($request);

        $form = $formService->create(CreateProductReviewFormType::class);
        $form->handleRequest($request);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!UserService::validateEmail($data['email'])) {
                $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

                return new JsonResponse($errorsList, 400);
            }

            /**
             * @var ShopProduct $shopProduct
             */
            $shopProduct = $this->entityManager->getRepository('App:ShopProduct')
                                               ->findActiveByUuid($data['productUuid']);
            if (!$shopProduct) {
                return new JsonResponse(['error' => true, 'message' => 'Product was not found.'], 404);
            }

            $productReview = new ProductReview(
                $data['name'],
                $data['email'],
                $data['rating'],
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

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getDataFromRequestToCreateProductReview(Request $request): array
    {
        return [
            'name' => htmlspecialchars((string)$request->request->get('name')),
            'email' => htmlspecialchars((string)$request->request->get('email')),
            'message' => htmlspecialchars((string)$request->request->get('message')),
            'rating' => (int)htmlspecialchars((string)$request->request->get('rating')),
            'dataProcessingAgreement' => RequestService::isDataProcessingAgreementValid($request->request->get('dataProcessingAgreement')),
            'productUuid' => htmlspecialchars((string)$request->request->get('productUuid'))
        ];
    }
}
