<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\ShopProduct;
use App\Form\Type\CreateCommentFormType;
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
 * Class CommentController
 *
 * @package App\Controller\Shop
 *
 * @Route("/comments")
 *
 * @OA\Tag(name="Comment")
 */
class CommentController
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $form;

    /**
     * CommentController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->form = $formFactory;
    }

    /**
     * @Route("/create", name="app_shop_comment_create", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to create comment",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             type="object",
     *             required={"name", "email", "message", "productUuid", "dataProcessingAgreement"},
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
     *                 example="This is comment message"
     *             ),
     *             @OA\Property(
     *                 property="productUuid",
     *                 description="Uuid of product to comment",
     *                 type="string",
     *                 example="ec9e6e5f-da5d-4f43-8249-24bb561cf9d8"
     *             ),
     *             @OA\Property(
     *                 property="dataProcessingAgreement",
     *                 description="Accept data terms",
     *                 type="boolean",
     *                 example=true
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Comment was created",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "uuid": "77c2b1ba-a949-4f4b-905b-445df5ea2687"}
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Accept terms before create comment"}
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createComment(Request $request): JsonResponse
    {
        $em = $this->entityManager;
        $formService = $this->form;

        $data = $this->getDataFromRequest($request);

        $form = $formService->create(CreateCommentFormType::class);
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

            $comment = new Comment($data['name'], $data['email'], $data['message']);

            $em->persist($comment);

            $shopProduct->addComment($comment);

            $em->persist($shopProduct);
            $em->flush();

            return new JsonResponse(['error' => false, 'uuid' => $comment->getUuid()], 201);
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $form->getErrors(true)->count();
        if ($errorsCount > 0) {
            $errorsList['message'] = $form->getErrors(true)[0]->getOrigin()
                                                              ->getName() . ' - ' . $form->getErrors(true)[0]->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getDataFromRequest(Request $request): array
    {
        return [
            'name' => htmlspecialchars((string)$request->request->get('name')),
            'email' => htmlspecialchars((string)$request->request->get('email')),
            'message' => htmlspecialchars((string)$request->request->get('message')),
            'dataProcessingAgreement' => RequestService::isDataProcessingAgreementValid($request->request->get('dataProcessingAgreement')),
            'productUuid' => htmlspecialchars((string)$request->request->get('productUuid'))
        ];
    }
}
