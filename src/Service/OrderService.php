<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ClientUser;
use App\Entity\Order;
use App\Entity\OrderAddress;
use App\Entity\OrderPersonalDataInfo;
use App\Entity\ShopProduct;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class OrderService
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;

    public function __construct(UserService $userService, EntityManagerInterface $entityManager) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws Exception
     */
    public function createOrderAndReturnResponse($data): JsonResponse {
        $em = $this->entityManager;

        $personalData = $data['personalData'];

        $clientUser = $this->validateUserOrCreateIt($data['userToken'], $personalData['email']);
        if($clientUser instanceof JsonResponse) {
            return $clientUser;
        }

        $products = $this->validateProductsAndDecreaseQuantityOfProducts(
            json_decode($data['products'], true, 512, JSON_THROW_ON_ERROR)
        );
        if($products instanceof JsonResponse) {
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

        return new JsonResponse(['uuid' => $order->getUuid()], 201);
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
        if($userToken) {
            $encoderData = $this->userService->decodeUserByJWTToken($userToken);
            if(!$encoderData) {
                return new JsonResponse(['error' => true, 'message' => 'User token is not valid. Please login in and try again.'.$userToken], 401);
            }

            if($encoderData['email'] !== $email) {
                return new JsonResponse([
                    'error' => true,
                    'message' => "You cannot create an order from a different email address."
                ], 406);
            }
        }

        $clientUser = $this->entityManager->getRepository('App:ClientUser')->findOneBy([
            'email' => $email
        ]);

        if($clientUser && !$userToken) {
            return new JsonResponse(['error' => true, 'message' => 'Please login before creating an order.'], 401);
        }

        if(!$clientUser && $userToken) {
            return new JsonResponse(['error' => true, 'message' => 'User was not found with this email.'], 404);
        }

        if(!$clientUser) {
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

        foreach($products as $productData) {
            $productUuid = $productData['uuid'];
            $quantity = (int)$productData['quantity'];

            /**
             * @var ShopProduct $product
             */
            $product = $em->getRepository('App:ShopProduct')->findOneBy([
                'uuid' => $productUuid
            ]);
            if(!$product) {
                return new JsonResponse(['error' => true, 'message' => 'Product was not found.'], 404);
            }

            if($product->getQuantity() < $quantity) {
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
}
