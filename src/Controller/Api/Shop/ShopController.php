<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\ProductReview;
use App\Form\Type\ProductReviewType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ShopController
 *
 * @package App\Controller
 *
 * @Route("/api/shop")
 */
class ShopController
{
    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $form;

    /**
     * ShopController constructor.
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
     * @Route("/product-review/create", name="shop_add_product_review", methods={"POST"})
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function createProductReview(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $data = [
            'name' => htmlspecialchars((string)$request->get('name'), ENT_QUOTES),
            'email' => htmlspecialchars((string)$request->get('email'), ENT_QUOTES),
            'rating' => (int)htmlspecialchars((string)$request->get('rating'), ENT_QUOTES),
            'phoneNumber' => htmlspecialchars((string)$request->get('phoneNumber'), ENT_QUOTES),
            'message' => htmlspecialchars((string)$request->get('message'), ENT_QUOTES)
        ];

        if(!UserService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        $productReview = new ProductReview(
            $data['name'],
            $data['email'],
            $data['rating'],
            $data['message'],
            false
        );

        $productReviewForm = $form->create(ProductReviewType::class, $productReview);
        $productReviewForm->handleRequest($request);
        $productReviewForm->submit($data);

        $errors = $validator->validate($productReview);

        if ($productReviewForm->isSubmitted() && $productReviewForm->isValid()) {
            $em->persist($productReview);
            $em->flush();

            return new JsonResponse($productReview, 201);
        }

        $errorsList = ['error' => true, 'message' => []];

        /**
         * @var ConstraintViolation $error
         */
        foreach($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
