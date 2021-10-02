<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\ShopProduct;
use App\Form\Type\CreateCommentFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 *
 * @package App\Controller\Shop
 *
 * @Route("/api/comments")
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createProductReview(Request $request): JsonResponse {
        $em = $this->entityManager;
        $formService = $this->form;

        $data = $this->getDataFromRequest($request);

        $form = $formService->create(CreateCommentFormType::class);
        $form->handleRequest($request);
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()) {
            if(!UserService::validateEmail($data['email'])) {
                $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

                return new JsonResponse($errorsList, 400);
            }

            /**
             * @var ShopProduct $shopProduct
             */
            $shopProduct = $this->entityManager->getRepository('App:ShopProduct')
                                               ->findActiveByUuid($data['productUuid']);
            if(!$shopProduct) {
                return new JsonResponse(['error' => true, 'message' => 'Product was not found.']);
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
        if($errorsCount > 0) {
            $errorsList['message'] = $errorsCount === 1 ?
                $form->getErrors(true)[0]->getMessage() :
                'Fill in all the required data';
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
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement'),
            'productUuid' => htmlspecialchars((string)$request->request->get('productUuid'))
        ];
    }
}
