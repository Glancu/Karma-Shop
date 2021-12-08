<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Faker\Factory;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ClientUserControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/user';

    private array $defaultData;

    protected function setUp(): void
    {
        $faker = Factory::create();

        $this->defaultData = [
            'email' => $faker->email,
            'password' => $faker->password
        ];
    }

    /**
     * @test
     */
    public function it_allow_to_create_user_with_full_data(): void
    {
        $data = $this->defaultData;

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_email(): void
    {
        $data = $this->defaultData;
        unset($data['email']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_wit_bad_email(): void
    {
        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_password(): void
    {
        $data = $this->defaultData;
        unset($data['password']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @param array $data
     * @param bool $allowFalse
     */
    private function checkAssertByData(array $data, $allowFalse = false): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            self::CREATE_API_URL.'/create',
            [],
            [],
            [],
            json_encode($data)
        );

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $bool = true;
            if(isset($result['error'], $result['message']) && $result['message'] !== 'User already exist with this email.') {
                $bool = false;
            }

            if($allowFalse) {
                self::assertFalse(false);
                return;
            }

            self::assertTrue($bool);
        } catch (JsonException $exception) {
            self::assertTrue(false);
        }
    }
}
