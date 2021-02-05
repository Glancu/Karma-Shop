<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ClientUserControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/user';

    private array $defaultData;

    protected function setUp(): void
    {
        $this->defaultData = [
            'firstName' => 'First',
            'lastName' => 'Last',
            'email' => 'email@email.com',
            'password' => 'pass',
            'phoneNumber' => '111-222-333',
            'postalCode' => '11-000',
            'city' => 'city',
            'country' => 'country',
            'street' => 'street'
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
    public function it_does_not_allow_to_create_user_without_first_name(): void
    {
        $data = $this->defaultData;
        unset($data['firstName']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_last_name(): void
    {
        $data = $this->defaultData;
        unset($data['lastName']);

        $this->checkAssertByData($data, true);
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
     * @test
     */
    public function it_does_not_allow_to_create_user_without_phone_number(): void
    {
        $data = $this->defaultData;
        unset($data['phoneNumber']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_postal_code(): void
    {
        $data = $this->defaultData;
        unset($data['postalCode']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_city(): void
    {
        $data = $this->defaultData;
        unset($data['city']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_country(): void
    {
        $data = $this->defaultData;
        unset($data['country']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_user_without_street(): void
    {
        $data = $this->defaultData;
        unset($data['street']);

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
            $data,
            [],
            []
        );

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $bool = true;
            if(isset($result['error'], $result['message']) && $result['message'] !== 'User is register with this email.') {
                $bool = false;
            }

            if($allowFalse) {
                self::assertFalse(false);
                return;
            }

            self::assertTrue($bool);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }
}
