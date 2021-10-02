<?php
declare(strict_types=1);

namespace App\Controller\Api\Shop;

use App\Form\Type\CreateOrderType;
use App\Service\OrderService;
use JsonException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * OrderController constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->form = $formFactory;
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
    public function createOrder(Request $request, OrderService $orderService): JsonResponse {
        $formService = $this->form;

        $data = $this->getDataFromRequestToCreateOrder($request);

        $form = $formService->create(CreateOrderType::class);
        $form->handleRequest($request);
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()) {
            return $orderService->createOrderAndReturnResponse($data);
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $form->getErrors(true)->count();
        if($errorsCount > 0) {
            $errorsList['message'] = $errorsCount === 1 ?
                $form->getErrors(true)[0]->getMessage() :
                'Fill in all the required data';
        }

        return new JsonResponse($errorsList, 400);
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
