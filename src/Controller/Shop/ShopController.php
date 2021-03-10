<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Entity\ProductReview;
use App\Form\Type\ProductReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @Route("/product-review/create", name="shop_add_product_reviewt", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function createContact(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $em = $this->entityManager;
        $form = $this->form;
        $productReview = new ProductReview();

        $name = $request->get('name');
        $email = $request->get('email');
        $rating = (int)$request->get('rating');
        $phoneNumber = $request->get('phoneNumber');
        $message = $request->get('message');

        $data = [
            'name' => $name,
            'email' => $email,
            'rating' => $rating,
            'phoneNumber' => $phoneNumber,
            'message' => $message
        ];

        $productReviewForm = $form->create(ProductReviewType::class, $productReview);
        $productReviewForm->handleRequest($request);
        $productReviewForm->submit($data);

        $errors = $validator->validate($productReview);

        if ($productReviewForm->isSubmitted() && $productReviewForm->isValid()) {
            $productReview->setName($name);
            $productReview->setEmail($email);
            $productReview->setRating($rating);
            $productReview->setPhoneNumber($phoneNumber);
            $productReview->setMessage($message);

            $em->persist($productReview);
            $em->flush();

            $return = $serializer->serialize($productReview, 'json');

            return new Response($return);
        }

        $errorsList = ['error' => true, 'message' => []];

        /**
         * @var ConstraintViolation $error
         */
        foreach($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        $return = $serializer->serialize($errorsList, 'json');

        return new Response($return);
    }
}
