<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OrderController
 *
 * @package App\Controller
 *
 * @Route("/api/shop/order")
 */
class OrderController
{
    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $form;

    /**
     * NewsletterController constructor.
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
     * @Route("/", name="app_shop_order_create", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function createOrder(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $em = $this->entityManager;
        $form = $this->form;
        $order = new Order();

        $data = [
            'name' => $request->request->get('name'),
            'email' => $request->request->get('email'),
            'dataProcessingAgreement' => $request->request->get('dataProcessingAgreement'),
        ];

        $orderForm = $form->create(Order::class, $order);
        $orderForm->handleRequest($request);
        $orderForm->submit($data);

        $errors = $validator->validate($order);
        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            // @TODO Implement it


            dump('Implement it');
            exit;
//            $em->persist($order);
//            $em->flush();
//
            $createdObjectJson = $serializer->serialize($order, 'json');

            return new Response($createdObjectJson);
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
