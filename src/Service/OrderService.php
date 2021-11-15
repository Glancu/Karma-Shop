<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ClientUser;
use App\Entity\EmailTemplate;
use App\Entity\Order;
use App\Entity\OrderAddress;
use App\Entity\OrderPersonalDataInfo;
use App\Entity\ShopProduct;
use App\Message\SendOrderMailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class OrderService
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private MailerService $mailerService;
    private Environment $templating;
    private MessageBusInterface $messageBus;

    public function __construct(
        UserService $userService,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        Environment $templating,
        MessageBusInterface $messageBus
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        $this->templating = $templating;
        $this->messageBus = $messageBus;
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     *
     * @throws JsonException
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

        $products = $this->validateProductsAndDecreaseQuantityOfProducts($data['products']);
        if ($products instanceof JsonResponse) {
            return $products;
        }

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
            $personalData['addressLineSecond'],
            $personalData['city'],
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
            $products,
            $personalData['additionalInformation'] ?? null
        );

        $em->persist($order);
        $em->flush();

        $this->sendMailToUser($order, $clientUser->getEmail());
        $this->sendMailToAdmin($order);

        return new JsonResponse(['error' => false, 'uuid' => $order->getUuid()], 201);
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
        $emailSubject = Order::replaceVariablesForEmail($order, $emailTemplate->getSubject());
        $emailContent = Order::replaceVariablesForEmail($order, $emailTemplate->getMessage());

        $cartEmail = $this->templating->render('email/_order_cart.html.twig', [
            'order' => $order
        ]);

        $emailContent = str_replace('%cart%', $cartEmail, $emailContent);

        $this->messageBus->dispatch(new SendOrderMailMessage($emailSubject, $emailContent, $emailTo));
    }
}
