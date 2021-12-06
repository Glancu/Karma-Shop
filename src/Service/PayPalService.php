<?php
declare(strict_types=1);

namespace App\Service;

use App\Component\OrderStatus;
use App\Entity\Order;
use App\Entity\Transaction;
use Beelab\PaypalBundle\Paypal\Service;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PayPalService
{
    private EntityManagerInterface $entityManager;
    private Service $service;

    public function __construct(EntityManagerInterface $entityManager, Service $service)
    {
        $this->entityManager = $entityManager;
        $this->service = $service;
    }

    /**
     * @param Order $order
     *
     * @return ResponseInterface|JsonResponse|RedirectResponse
     */
    public function createPayment(Order $order, array $optionalParameters = [])
    {
        $em = $this->entityManager;
        $service = $this->service;

        $transactionOrder = $order->getTransaction();
        if ($transactionOrder && $transactionOrder->getStatus() === Transaction::STATUS_OK) {
            if ($optionalParameters && isset($optionalParameters['returnUrl'])) {
                $explodedReturnUrl = explode('?redirectUrl=', $optionalParameters['returnUrl']);

                $additionalUrl = '?error=true&message=This order is already paid.';

                return new RedirectResponse($explodedReturnUrl[1] . $additionalUrl);
            }

            return new JsonResponse(['error' => true, 'message' => 'This order is already paid.']);
        }

        $transaction = new Transaction($order->getPriceGross() / MoneyService::PRICE_DIVIDE_MULTIPLY);
        try {
            $config = self::getConfig($order);

            if ($optionalParameters && isset($optionalParameters['returnUrl'])) {
                $config['returnUrl'] = $optionalParameters['returnUrl'];
            }

            if ($optionalParameters && isset($optionalParameters['cancelUrl'])) {
                $config['cancelUrl'] = $optionalParameters['cancelUrl'];
            }

            $response = $service->setTransaction($transaction, $config)->start();

            $transaction->setOrder($order);

            $em->persist($transaction);

            $order->setTransaction($transaction);

            $em->persist($order);
            $em->flush();

            return $response;
        } catch (Exception $e) {
            throw new HttpException(503, $e->getMessage());
        }
    }

    public function completedPayment(string $token): JsonResponse
    {
        $em = $this->entityManager;
        $service = $this->service;

        $transaction = $em->getRepository('App:Transaction')->findOneByToken($token);
        if (null === $transaction) {
            return new JsonResponse(['error' => true, 'message' => 'Transaction was not found with this token.'], 404);
        }

        $service->setTransaction($transaction)->complete();


        /**
         * @var Order $order
         */
        $order = $transaction->getOrder();
        if ($order && $transaction->isOk()) {
            $order->setStatus(OrderStatus::STATUS_PAID);
            $em->persist($order);
        }

        $em->flush();

        if (!$transaction->isOk()) {
            $transactionResponse = $transaction->getResponse();
            if (isset($transactionResponse, $transactionResponse['L_LONGMESSAGE0']) &&
                $transactionResponse['L_LONGMESSAGE0'] === 'Payment has already been made for this InvoiceID.'
            ) {
                return new JsonResponse([
                    'error' => true,
                    'message' => 'Payment has already been made for this order.'
                ]);
            }

            return new JsonResponse([
                'error' => true,
                'message' => 'Error while complete payment. Try again or contact with us.'
            ]);
        }

        return new JsonResponse(['error' => false, 'message' => 'Order has been paid.']);
    }

    public function canceledPayment(string $token): JsonResponse
    {
        $em = $this->entityManager;

        $transaction = $em->getRepository('App:Transaction')->findOneByToken($token);
        if (null === $transaction) {
            return new JsonResponse(['error' => true, 'message' => 'Transaction was not found with this token.'], 404);
        }

        /**
         * @var Order $order
         */
        $order = $transaction->getOrder();
        if ($order) {
            $order->setTransaction(null);
            $order->setStatus(OrderStatus::STATUS_NOT_PAID);
            $em->persist($order);
        }

        $transaction->cancel();

        $em->flush();

        return new JsonResponse(['error' => false, 'message' => 'Your payment for order was canceled.']);
    }

    public static function getConfig(Order $order): array
    {
        $shippingAddress = [
            'recipient_name' => 'test' . $order->getOrderPersonalDataInfo()
                                               ->getFirstName() . ' ' . $order->getOrderPersonalDataInfo()
                                                                              ->getLastName(),
            'line1' => $order->getOrderAddress()->getAddressLineFirstCorrespondence(),
            'city' => $order->getOrderAddress()->getCity(),
            'postal_code' => $order->getOrderAddress()->getPostalCode(),
            'phone' => $order->getOrderPersonalDataInfo()->getPhoneNumber()
        ];

        return [
            'currency' => getenv('CURRENCY_NAME'),
            'shipping_address' => $shippingAddress,
            'payee' => ['email' => 'a' . $order->getUser()->getEmail()]
        ];
    }
}
