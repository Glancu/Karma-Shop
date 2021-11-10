<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Entity\Order;
use App\Form\Type\CreateOrderType;
use App\Service\OrderService;
use App\Service\RequestService;
use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
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
 * @Route("/shop")
 *
 * @OA\Tag(name="Shop")
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
     * @OA\RequestBody(
     *     description="Pass data to create an user",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             type="object",
     *             required={"personalData", "methodPayment", "products", "dataProcessingAgreement"},
     *             @OA\Property(
     *                 property="personalData",
     *                 description="Personal data",
     *                 type="object",
     *                 example={"firstName": "firstname", "lastName": "lastname", "companyName": null, "phoneNumber": "phone", "email": "email@email.com", "addressLineFirst": "adres1", "addressLineSecond": "addres2", "city": "city", "postalCode": null, "additionalInformation": null, "firstNameCorrespondence": null, "lastNameCorrespondence": null, "companyNameCorrespondence": null, "addressLineFirstCorrespondence": null, "addressLineSecondCorrespondence": null, "cityCorrespondence": null, "postalCodeCorrespondence": null}
     *             ),
     *             @OA\Property(
     *                 property="methodPayment",
     *                 description="Method payment",
     *                 type="string",
     *                 enum={"Online", "PayPal"}
     *             ),
     *             @OA\Property(
     *                 property="isCustomCorrespondence",
     *                 description="Define is custom correspondence",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 description="Products",
     *                 type="object",
     *                 example={{"uuid":"c0b8ed63-b795-408c-b720-f7a2ec4c5706","quantity":2},{"uuid":"f3de45cf-6efd-49ec-826d-43632512596a","quantity":1},{"uuid":"959ab36b-1c5a-4dce-b072-1a863751f1bf","quantity":1}}
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
     *     description="Order was ordered",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "uuid": "6984403b-113a-450d-8492-92aa05b76fe3"}
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
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Please login before creating an order."}
     *     )
     * )
     * @OA\Response(
     *     response=409,
     *     description="Conflict",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "There are not enough product quantities to order. You can order a maximum PRODUCT_QUANTITY of PRODUCT_NAME"}
     *     )
     * )
     *
     * @Security()
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
            $errorsList['message'] = $form->getErrors(true)[0]->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }

    /**
     * @Route("/user-orders", name="shop_user_orders", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="List of orders",
     *     @OA\JsonContent(
     *        type="string",
     *        example={{ "uuid": "ae58a6b1-9356-4f63-9ce5-79437bfdb289", "priceGross": null, "createdAt": "2021-09-08 08:12:44", "status": "New", "methodPayment": "Online", "orderNumber": "MM1IaB9ViGdBJuP", "products": { { "name": "product 2", "slug": "product-2", "quantity": 196, "description": "<p>description of product 2</p>", "shopBrand": { "title": "Gionee", "slug": "gionee", "enable": true, "uuid": "9bbc3dce-c3ed-4ffb-be3e-2257764fdec7" }, "shopCategory": { "title": "Meat and Fish", "slug": "meat-and-fish", "enable": true, "uuid": "c0cb68af-bea3-43fe-b8eb-6a8a1c40268c" }, "shopProductSpecifications": {}, "reviews": {}, "shopColors": {}, "comments": {}, "enable": true, "uuid": "f3de45cf-6efd-49ec-826d-43632512596a", "priceNet": "0.71", "priceGross": "0.75", "images": { { "name": "image.jpg", "url": "https://website.com/image.jpg" } } } } }}
     *     )
     * )
     *
     * @Security(name="Bearer")
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

            foreach ($order->getProducts() as $product) {
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
            'dataProcessingAgreement' => RequestService::isDataProcessingAgreementValid($request->request->get('dataProcessingAgreement')),
            'userToken' => $request->request->get('userToken') !== 'null' ?
                htmlspecialchars((string)$request->request->get('userToken'), ENT_QUOTES) : null
        ];
    }
}
