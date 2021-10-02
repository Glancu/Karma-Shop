<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ProductReview;
use App\Entity\ShopProduct;
use App\Form\Type\CreateProductReviewFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductReviewController
 *
 * @package App\Controller
 *
 * @Route("/api/product-review")
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createProductReview(Request $request): JsonResponse {
        $em = $this->entityManager;
        $formService = $this->form;

        $data = $this->getDataFromRequestToCreateProductReview($request);

        $form = $formService->create(CreateProductReviewFormType::class);
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
    private function getDataFromRequestToCreateProductReview(Request $request): array
    {
        return [
            'name' => htmlspecialchars((string)$request->request->get('name')),
            'email' => htmlspecialchars((string)$request->request->get('email')),
            'message' => htmlspecialchars((string)$request->request->get('message')),
            'rating' => (int)htmlspecialchars((string)$request->request->get('rating')),
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement'),
            'productUuid' => htmlspecialchars((string)$request->request->get('productUuid'))
        ];
    }
}
