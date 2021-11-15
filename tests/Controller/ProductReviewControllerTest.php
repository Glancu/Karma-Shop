<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\ShopProduct;
use App\Repository\ShopProductRepository;
use Exception;
use Faker\Factory;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductReviewControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/shop/product-review';

    private array $defaultData;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

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

        $this->defaultData = [
            'name' => $faker->name,
            'email' => $faker->email,
            'message' => $faker->text,
            'rating' => random_int(1, 4),
            'productUuid' => $firstProduct->getUuid(),
            'dataProcessingAgreement' => true
        ];
    }

    /**
     * @test
     *
     * @throws JsonException
     */
    public function it_allow_to_create_product_review_with_full_data(): void
    {
        $data = $this->defaultData;

        $this->checkAssertByData($data);
    }

    /**
     * @param array $data
     *
     * @throws JsonException
     */
    private function checkAssertByData(array $data): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            self::CREATE_API_URL.'/create',
            [],
            [],
            [],
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $bool = true;
            if(isset($result['error'], $result['message'])) {
                $bool = false;
            }

            self::assertTrue($bool);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }
}
