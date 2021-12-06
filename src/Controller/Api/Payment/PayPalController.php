<?php
declare(strict_types=1);

namespace App\Controller\Api\Payment;

use App\Service\PayPalService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Omnipay\Common\Message\ResponseInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PayPalController
 *
 * @package App\Controller\Api\Payment
 *
 * @Route("/payment/pay-pal")
 *
 * @OA\Tag(name="PayPal")
 *
 * @Security()
 */
class PayPalController
{
    private EntityManagerInterface $entityManager;
    private PayPalService $payPalService;
    private RouterInterface $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        PayPalService $payPalService,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->payPalService = $payPalService;
        $this->router = $router;
    }

    /**
     * @Route("/create/{orderUuid}", name="app_payment_pay_pal_create", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="orderUuid",
     *     in="path",
     *     description="Order of uuid",
     *     required=true,
     *     @OA\Schema(type="string", example="3c8672b1-d0c6-4d27-afa9-0972fe55ee78")
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Redirect to paypal website to pay for order."
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Something went wrong. Try again or contact with us."}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Not found order by uuid 3c8672b1-d0c6-4d27-afa9-0972fe55ee78"}
     *     )
     * )
     *
     * @param Request $request
     * @param string $orderUuid
     *
     * @return RedirectResponse|JsonResponse
     */
    public function create(Request $request, string $orderUuid)
    {
        $orderUuid = htmlspecialchars($orderUuid, ENT_QUOTES);
        $notifyUrl = htmlspecialchars($request->get('notifyUrl'), ENT_QUOTES);

        $order = $this->entityManager->getRepository('App:Order')->findByUuid($orderUuid);
        if (!$order) {
            if ($notifyUrl) {
                $additionalQueries = "?error=true&message=Not found order.";

                return new RedirectResponse($notifyUrl . $additionalQueries);
            }

            return new JsonResponse(
                ['error' => true, 'message' => "Not found order by uuid ${orderUuid}"],
                404
            );
        }

        $optionalParameters = [];

        if ($notifyUrl) {
            $returnUrl = $this->router->generate(
                'api_app_payment_pay_pal_completed',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $cancelUrl = $this->router->generate(
                'api_app_payment_pay_pal_canceled',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $optionalParameters = [
                'returnUrl' => $returnUrl . "?redirectUrl=${notifyUrl}",
                'cancelUrl' => $cancelUrl . "?redirectUrl=${notifyUrl}"
            ];
        }

        $payment = $this->payPalService->createPayment($order, $optionalParameters);
        if ($payment instanceof ResponseInterface) {
            return new RedirectResponse($payment->getRedirectUrl());
        }

        if ($payment instanceof JsonResponse || $payment instanceof RedirectResponse) {
            return $payment;
        }

        return new JsonResponse(
            ['error' => true, 'message' => "Something went wrong. Try again or contact with us."],
            400
        );
    }

    /**
     * @Route("/completed", name="app_payment_pay_pal_completed", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="token",
     *     in="query",
     *     description="Token of order trasaction",
     *     required=true,
     *     @OA\Schema(type="string", example="EC-3XN124432V376214V")
     * )
     * @OA\Parameter(
     *     name="PayerID",
     *     in="query",
     *     description="PayerID generated by PayPal",
     *     required=true,
     *     @OA\Schema(type="string", example="LRA9368HFCWXE")
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "message": "Order has been paid."}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Transaction was not found with this token."}
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse
     */
    public function completed(Request $request)
    {
        $token = htmlspecialchars($request->get('token'), ENT_QUOTES);

        $payment = $this->payPalService->completedPayment($token);

        $redirectUrl = htmlspecialchars($request->get('redirectUrl'), ENT_QUOTES);
        if ($redirectUrl) {
            $content = json_decode($payment->getContent());
            $queryUrl = '';

            if (property_exists($content, 'error') && property_exists($content, 'message')) {
                $queryUrl = '?error=' . $content->error . '&message=' . $content->message;
            }

            return new RedirectResponse($redirectUrl . $queryUrl);
        }

        return $payment;
    }

    /**
     * @Route("/canceled", name="app_payment_pay_pal_canceled", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="token",
     *     in="query",
     *     description="Token of order trasaction",
     *     required=true,
     *     @OA\Schema(type="string", example="EC-3XN124432V376214V")
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "message": "Your payment for order was canceled."}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Transaction was not found with this token."}
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse
     */
    public function canceled(Request $request)
    {
        $token = htmlspecialchars($request->get('token'), ENT_QUOTES);

        $payment = $this->payPalService->canceledPayment($token);

        $redirectUrl = htmlspecialchars($request->get('redirectUrl'), ENT_QUOTES);
        if ($redirectUrl) {
            $content = json_decode($payment->getContent());
            $queryUrl = '';

            if (property_exists($content, 'error') && property_exists($content, 'message')) {
                $queryUrl = '?error=' . (bool)$content->error . '&message=' . $content->message;
            }

            return new RedirectResponse($redirectUrl . $queryUrl);
        }

        return $payment;
    }
}
