<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Component\OrderStatus;
use App\Entity\Order;
use App\Entity\ShopProductItem;
use App\Form\Type\CreateOrderType;
use App\Serializer\ShopSerializeDataResponse;
use App\Service\ImageService;
use App\Service\MoneyService;
use App\Service\OrderService;
use App\Service\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
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
    private ShopSerializeDataResponse $shopSerializeDataResponse;
    private RouterInterface $router;

    /**
     * OrderController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param ShopSerializeDataResponse $shopSerializeDataResponse
     * @param RouterInterface $router
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        ShopSerializeDataResponse $shopSerializeDataResponse,
        RouterInterface $router
    ) {
        $this->form = $formFactory;
        $this->entityManager = $entityManager;
        $this->shopSerializeDataResponse = $shopSerializeDataResponse;
        $this->router = $router;
    }

    /**
     * @Route("/create-order", name="app_shop_order_create", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to create an user",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"personalData", "methodPayment", "products", "dataProcessingAgreement"},
     *             @OA\Property(
     *                 property="personalData",
     *                 description="Personal data",
     *                 type="object",
     *                 example={"firstName": "firstname", "lastName": "lastname", "companyName": null, "phoneNumber": "phone", "email": "email@email.com", "addressLineFirst": "adres1", "addressLineSecond": null, "city": "city", "postalCode": null, "additionalInformation": null, "firstNameCorrespondence": null, "lastNameCorrespondence": null, "companyNameCorrespondence": null, "addressLineFirstCorrespondence": null, "addressLineSecondCorrespondence": null, "cityCorrespondence": null, "postalCodeCorrespondence": null}
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
     *             ),
     *              @OA\Property(
     *                 property="userToken",
     *                 description="User JWT token",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzY1NDkwMTQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJlbWFpbCI6ImVtQGVtLnBsIiwiaWUiOnsiZGF0ZSI6IjIwMjEtMTEtMTAgMTI6NTY6NTQuMjg3OTM4IiwidGltZXpvbmVfdHlwZSI6MywidGltZXpvbmUiOiJVVEMifSwiZXhwIjoxNjM2NTUyNjE0fQ.lTv22ObYCha-4gKi7bSXTyqqr60BLl45q3zOwbRYGwoMjjzIPCoUTCoDhyZchgMEWrdjVp7wbZG3Sp_x2rNXiRqQWRukHfFjYMQGkfi_s5_5q2e1ptt3Tuhmw30XI1CAVopN-rWrCfFV4zWicv9py3KGMgkNZ2KnrVUfKuOwHFLpeZ3VPJwRP7hXCrQam0YkSKq_YQKeY0BOL3q2i-fxpV9PIdcCZqe2bKlRmWW_IC1TCUNmNJtTPl5NrLhb0hBpC_wsK1RnwfOSddg-7dnoPLcZcwcKkpwxa0hAG0NHiWQwy7esmDdGnyR4T_z2vFnFzslp000G4RjjGznhlzZMoS3sUl0cIbxOrJ0D_goNnCyqm24bJnZi52ZP5kXkUTLT4mjus8p0areHZKQ12xlEQ64ZsTiUFcM6OQpOPb1xfQgU9WPOuUxcjYMDq1Ide6OC375Y11mcYbV6azF0_JZfc2ksnMlAfae9WdSHyM1f0mAZH8cxZzxSrhJOyf22FptQthpUQemcgj3gpQUxoxCStsRkgVahwThuRBcirlsXak9Of0mHnbB2HzJ0FaZk3IPlE1peB0sedtFfzRd_niirXna2P16a0PMqULZz-sygsOKgSP7ufnkvaAQsu43kUWaAZpe6zy40KrC5tS6PrdAD8i9bd5HURXTaSe53YM5QYmg"
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
     * @param RequestService $requestService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws Exception
     */
    public function createOrder(
        Request $request,
        OrderService $orderService,
        RequestService $requestService
    ): JsonResponse {
        $formService = $this->form;

        $requiredDataFromContent = [
            'personalData',
            'methodPayment',
            'isCustomCorrespondence',
            'products',
            'dataProcessingAgreement'
        ];

        $data = $requestService->validRequestContentAndGetData($request->getContent(), $requiredDataFromContent);
        if ($data instanceof JsonResponse) {
            return $data;
        }

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
     *        example={{ "uuid": "ae58a6b1-9356-4f63-9ce5-79437bfdb289", "priceGross": null, "createdAt": "2021-09-08 08:12:44", "status": "New", "methodPayment": "Online", "products": { { "name": "product 2", "slug": "product-2", "quantity": 196, "description": "<p>description of product 2</p>", "shopBrand": { "title": "Gionee", "slug": "gionee", "enable": true, "uuid": "9bbc3dce-c3ed-4ffb-be3e-2257764fdec7" }, "shopCategory": { "title": "Meat and Fish", "slug": "meat-and-fish", "enable": true, "uuid": "c0cb68af-bea3-43fe-b8eb-6a8a1c40268c" }, "shopProductSpecifications": {}, "reviews": {}, "shopColors": {}, "comments": {}, "enable": true, "uuid": "f3de45cf-6efd-49ec-826d-43632512596a", "priceNet": "0.71", "priceGross": "0.75", "images": { { "name": "image.jpg", "url": "https://website.com/image.jpg" } } } } }}
     *     )
     * )
     *
     * @Security(name="Bearer")
     *
     * @param UserInterface $user
     * @param ImageService $imageService
     *
     * @return JsonResponse
     */
    public function getOrdersProductsByUserToken(
        UserInterface $user,
        ImageService $imageService
    ): JsonResponse {
        $em = $this->entityManager;
        $clientEmail = $user->getEmail();

        $orders = $em->getRepository('App:Order')->findByClientEmail($clientEmail);

        $data = [];

        /**
         * @var Order $order
         */
        foreach ($orders as $order) {
            $products = [];

            /**
             * @var ShopProductItem $productItem
             */
            foreach ($order->getShopProductItems() as $productItem) {
                $product = $productItem->getProduct();

                $priceGross = MoneyService::convertIntToFloat($productItem->getPriceGross());

                $products[] = [
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'quantity' => $productItem->getQuantity(),
                    'priceGross' => number_format((float)$priceGross, 2, '.', ''),
                    'total' => number_format(($priceGross * $productItem->getQuantity()), 2, '.', ''),
                    'image' => $imageService->getImageNameAndUrl($product->getImages()->first())
                ];
            }

            $arr = [
                'uuid' => $order->getUuid(),
                'priceGross' => $order->getPriceGross(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'status' => $order->getStatusStr(),
                'methodPayment' => $order->getMethodPaymentStr(),
                'products' => $products
            ];

            if ($order->getMethodPayment() === Order::METHOD_PAYMENT_TYPE_PAYPAL && $order->getStatus() !== OrderStatus::STATUS_PAID) {
                $arr['payPalUrl'] = $this->router->generate('api_app_payment_pay_pal_create', [
                    'orderUuid' => $order->getUuid()
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }

            $data[] = $arr;
        }

        return new JsonResponse($data);
    }
}
