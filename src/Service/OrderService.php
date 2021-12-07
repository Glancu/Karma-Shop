<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ClientUser;
use App\Entity\EmailTemplate;
use App\Entity\Order;
use App\Entity\OrderAddress;
use App\Entity\OrderPersonalDataInfo;
use App\Entity\ShopProduct;
use App\Entity\ShopProductItem;
use App\Message\SendMailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class OrderService
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;
    private Environment $templating;
    private MessageBusInterface $messageBus;
    private RouterInterface $router;

    public function __construct(
        UserService $userService,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        Environment $templating,
        MessageBusInterface $messageBus,
        RouterInterface $router
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        $this->templating = $templating;
        $this->messageBus = $messageBus;
        $this->router = $router;
    }

    public function generatePayPalAbsoluteUrl(Order $order): string
    {
        return $this->router->generate('api_app_payment_pay_pal_create', [
            'orderUuid' => $order->getUuid()
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function createOrderAndSendMailAndReturnResponse($data): JsonResponse
    {
        $em = $this->entityManager;

        $personalData = $data['personalData'];

        $clientUser = $this->validateUserOrCreateIt($data['userToken'], $personalData['email']);
        if ($clientUser instanceof JsonResponse) {
            return $clientUser;
        }

        $shopProducts = $this->validateProductsAndDecreaseQuantityOfProducts($data['products']);
        if ($shopProducts instanceof JsonResponse) {
            return $shopProducts;
        }

        $shopProductItems = $this->createShopProductItemFromProductsArr($data['products']);

        $orderPersonalDataInfo = new OrderPersonalDataInfo(
            $personalData['firstName'],
            $personalData['lastName'],
            $personalData['phoneNumber'],
            $personalData['companyName'] ?? null,
            $personalData['firstNameCorrespondence'] ?? null,
            $personalData['lastNameCorrespondence'] ?? null,
            $personalData['companyNameCorrespondence'] ?? null
        );

        $orderAddress = new OrderAddress(
            $personalData['addressLineFirst'],
            $personalData['city'],
            $personalData['addressLineSecond'] ?? null,
            $personalData['postalCode'] ?? null,
            $personalData['addressLineFirstCorrespondence'] ?? null,
            $personalData['addressLineSecondCorrespondence'] ?? null,
            $personalData['cityCorrespondence'] ?? null,
            $personalData['postalCodeCorrespondence'] ?? null
        );

        $methodPayment = Order::getMethodPaymentInt($data['methodPayment']);

        $order = new Order(
            $clientUser,
            $orderPersonalDataInfo,
            $orderAddress,
            $methodPayment,
            $shopProductItems,
            $personalData['additionalInformation'] ?? null
        );

        $em->persist($order);
        $em->flush();

        $this->sendMailToUser($order, $clientUser->getEmail());
        $this->sendMailToAdmin($order);

        $returnArr = [
            'error' => false,
            'uuid' => $order->getUuid()
        ];

        if ($order->getMethodPayment() === Order::METHOD_PAYMENT_TYPE_PAYPAL) {
            $returnArr['payPalUrl'] = $this->generatePayPalAbsoluteUrl($order);
        }

        return new JsonResponse($returnArr, 201);
    }

    /**
     * @param string|null $userToken
     * @param string $email
     *
     * @return ClientUser|JsonResponse
     *
     * @throws Exception
     */
    private function validateUserOrCreateIt(?string $userToken, string $email)
    {
        if ($userToken) {
            $encoderData = $this->userService->decodeUserByJWTToken($userToken);
            if (!$encoderData) {
                return new JsonResponse([
                    'error' => true,
                    'message' => 'User token is not valid. Please login in and try again.' . $userToken
                ], 401);
            }

            if ($encoderData['email'] !== $email) {
                return new JsonResponse([
                    'error' => true,
                    'message' => "You cannot create an order from a different email address."
                ], 406);
            }
        }

        $clientUser = $this->entityManager->getRepository('App:ClientUser')->findOneBy([
            'email' => $email
        ]);

        if ($clientUser && !$userToken) {
            return new JsonResponse(['error' => true, 'message' => 'Please login before creating an order.'], 401);
        }

        if (!$clientUser && $userToken) {
            return new JsonResponse(['error' => true, 'message' => 'User was not found with this email.'], 404);
        }

        if (!$clientUser) {
            return $this->userService->createUser($email);
        }

        return $clientUser;
    }

    /**
     * @param array $products
     *
     * @return array|JsonResponse
     */
    private function validateProductsAndDecreaseQuantityOfProducts(array $products)
    {
        $em = $this->entityManager;
        $productsArr = [];

        foreach ($products as $productData) {
            $productUuid = $productData['uuid'];
            $quantity = (int)$productData['quantity'];

            /**
             * @var ShopProduct $product
             */
            $product = $em->getRepository('App:ShopProduct')->findOneBy([
                'uuid' => $productUuid
            ]);
            if (!$product) {
                return new JsonResponse(['error' => true, 'message' => 'Product was not found.'], 404);
            }

            if ($product->getQuantity() < $quantity) {
                return new JsonResponse([
                    'error' => true,
                    'message' => sprintf(
                        'There are not enough product quantities to order. You can order a maximum %d of %s',
                        $product->getQuantity(),
                        $product->getName()
                    )
                ], 409);
            }

            $product->setQuantity($product->getQuantity() - $quantity);

            $em->persist($product);

            $productsArr[] = $product;
        }

        return $productsArr;
    }

    /**
     * @param Order $order
     * @param string $clientEmail
     *
     * @throws Exception
     */
    private function sendMailToUser(Order $order, string $clientEmail): void
    {
        $emailTemplateUser = $this->entityManager->getRepository('App:EmailTemplate')
                                                 ->findByType(EmailTemplate::TYPE_NEW_ORDER_TO_USER);
        if ($emailTemplateUser) {
            $this->replaceVariableAndSendMail($order, $emailTemplateUser, $clientEmail);
        }
    }

    /**
     * @param Order $order
     *
     * @throws Exception
     */
    private function sendMailToAdmin(Order $order): void
    {
        $emailTemplateAdmin = $this->entityManager->getRepository('App:EmailTemplate')
                                                  ->findByType(EmailTemplate::TYPE_NEW_ORDER_TO_ADMIN);
        if ($emailTemplateAdmin) {
            $this->replaceVariableAndSendMail($order, $emailTemplateAdmin, $this->mailerService->getAdminEmail());
        }
    }

    /**
     * @param Order $order
     * @param EmailTemplate $emailTemplate
     * @param string $emailTo
     *
     * @throws Exception
     */
    public function replaceVariableAndSendMail(Order $order, EmailTemplate $emailTemplate, string $emailTo): void
    {
        $emailSubject = $this->mailerService->replaceVariablesOrderForEmail($order, $emailTemplate->getSubject());

        $emailContent = $this->mailerService->replaceVariablesOrderForEmail(
            $order,
            $emailTemplate->getMessage(),
            ['payPalUrl' => $this->generatePayPalAbsoluteUrl($order)]
        );

        $cartEmail = $this->templating->render('email/_order_cart.html.twig', [
            'order' => $order
        ]);

        $emailContent = str_replace('%cart%', $cartEmail, $emailContent);

        $this->messageBus->dispatch(new SendMailMessage($emailSubject, $emailContent, $emailTo));
    }

    private function createShopProductItemFromProductsArr(array $products): array
    {
        $em = $this->entityManager;

        $items = [];

        foreach ($products as $productData) {
            $productUuid = $productData['uuid'];
            $quantity = (int)$productData['quantity'];

            $product = $em->getRepository('App:ShopProduct')->findOneBy([
                'uuid' => $productUuid
            ]);
            if ($product) {
                $item = new ShopProductItem($product, $quantity);

                $em->persist($item);

                $items[] = $item;
            }
        }

        $em->flush();

        return $items;
    }
}
