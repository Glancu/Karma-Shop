<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\ShopProduct;
use App\Repository\ShopProductRepository;
use App\Service\OrderService;
use Faker\Factory;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class OrderServiceTest extends KernelTestCase
{
    /**
     * @test
     *
     * @throws JsonException
     */
    public function create_an_order(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        $orderService = $container->get(OrderService::class);
        $shopProductRepository = $container->get(ShopProductRepository::class);

        /**
         * @var ShopProduct $firstProduct
         */
        $firstProduct = $shopProductRepository->findOneBy([], ['quantity' => 'DESC']);
        if(!$firstProduct) {
            self::assertTrue(false);
            return;
        }

        $faker = Factory::create();

        $data =  [
            'personalData' => [
                'email' => $faker->email,
                'firstName' => $faker->firstNameMale,
                'lastName' => $faker->lastName,
                'phoneNumber' => $faker->phoneNumber,
                'addressLineFirst' => $faker->address,
                'city' => $faker->city
            ],
            'methodPayment' => current(Order::getMethodPaymentsArr()),
            'isCustomCorrespondence' => false,
            'products' => [
                [
                    'uuid' => $firstProduct->getUuid(),
                    'quantity' => 1
                ]
            ],
            'dataProcessingAgreement' => true,
            'userToken' =>  null
        ];

        $order = $orderService->createOrderAndSendMailAndReturnResponse($data);

        self::assertTrue($order instanceof JsonResponse && $order->getStatusCode() === 201);
    }
}
