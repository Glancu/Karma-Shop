<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\Order;
use App\Form\Type\CreateOrderType;
use App\Service\OrderService;
use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class OrderController
 *
 * @package App\Controller
 *
 * @Route("/api/shop")
 */
class OrderController
{
    private FormFactoryInterface $form;
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * OrderController constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        SerializeDataResponse $serializeDataResponse
    ) {
        $this->form = $formFactory;
        $this->entityManager = $entityManager;
        $this->serializeDataResponse = $serializeDataResponse;
    }

    /**
     * @Route("/create-order", name="app_shop_order_create", methods={"POST"})
     *
     * @param Request $request
     * @param OrderService $orderService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     */
    public function createOrder(Request $request, OrderService $orderService): JsonResponse
    {
        $formService = $this->form;

        $data = $this->getDataFromRequestToCreateOrder($request);

        $form = $formService->create(CreateOrderType::class);
        $form->handleRequest($request);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            return $orderService->createOrderAndSendMailAndReturnResponse($data);
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $form->getErrors(true)->count();
        if ($errorsCount > 0) {
            $errorsList['message'] = $errorsCount === 1 ?
                $form->getErrors(true)[0]->getMessage() :
                'Fill in all the required data';
        }

        return new JsonResponse($errorsList, 400);
    }

    /**
     * @Route("/user-orders", name="shop_user_orders", methods={"GET"})
     *
     * @param UserInterface $user
     *
     * @return JsonResponse
     */
    public function getOrdersProductsByUserToken(UserInterface $user): JsonResponse
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;
        $clientEmail = $user->getEmail();

        $orders = $em->getRepository('App:Order')->findByClientEmail($clientEmail);

        $data = [];

        /**
         * @var Order $order
         */
        foreach ($orders as $order) {
            $products = [];

            foreach($order->getProducts() as $product) {
                $products[] = $serializer->getSingleProductData($product);
            }

            $data[] = [
                'uuid' => $order->getUuid(),
                'priceGross' => $order->getPriceGross(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'status' => $order->getStatusStr(),
                'methodPayment' => $order->getMethodPaymentStr(),
                'orderNumber' => $order->getOrderNumber(),
                'products' => $products
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws JsonException
     */
    private function getDataFromRequestToCreateOrder(Request $request): array
    {
        return [
            'personalData' => json_decode($request->request->get('personalData'), true, 512, JSON_THROW_ON_ERROR),
            'methodPayment' => htmlspecialchars((string)$request->request->get('methodPayment'), ENT_QUOTES),
            'isCustomCorrespondence' => (bool)$request->request->get('isCustomCorrespondence'),
            'products' => $request->request->get('products'),
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement'),
            'userToken' => $request->request->get('userToken') !== 'null' ?
                htmlspecialchars((string)$request->request->get('userToken'), ENT_QUOTES) : null
        ];
    }
}
