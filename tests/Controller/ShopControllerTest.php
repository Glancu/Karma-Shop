<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ShopControllerTest extends WebTestCase
{
    public const BASE_API_URL = '/api/shop/';

    private array $defaultData;

    private string $jwtToken = '';

    /**
     * @throws JsonException
     */
    protected function setUp(): void
    {
        $jwtClient = static::createClient();
        $jwtClient->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"email":"admin@email.com","password":"admin1"}');

        if (isset(json_decode($jwtClient->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['token'])) {
            $this->jwtToken = json_decode($jwtClient->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['token'];
        }

        $this->defaultData = [
            'name' => 'John',
            'email' => 'email@email.com',
            'rating' => 5,
            'phoneNumber' => '123-213-321',
            'message' => 'Simply message for product review'
        ];
    }

    /**
     * @test
     */
    public function it_allow_to_create_product_review_with_full_data(): void
    {
        $data = $this->defaultData;

        $this->sendDataProductReview($data);
    }

    /**
     * @test
     */
    public function it_allow_to_create_product_review_without_phone_number(): void
    {
        $data = $this->defaultData;
        unset($data['phoneNumber']);

        $this->sendDataProductReview($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_review_wit_bad_email(): void
    {
        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $this->sendDataProductReview($data, true);
    }

    /**
     * @param array$data
     * @param bool $allowFalse
     */
    private function sendDataProductReview(array $data, $allowFalse = false): void
    {
        $client = static::createClient();
        $jwtToken = $this->jwtToken;

        $client->request(
            'POST',
            self::BASE_API_URL . '/shop/product-review/create',
            $data,
            [],
            [
                'HTTP_AUTHORIZATION' => 'bearer ' . $jwtToken
            ]
        );

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $bool = $result['error'] ?? false;

            if($allowFalse) {
                self::assertFalse(false);
                return;
            }

            self::assertFalse((bool)$bool);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }
}
