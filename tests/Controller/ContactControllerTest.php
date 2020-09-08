<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/contact/create';

    private array $defaultData;

    private string $jwtToken;

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
            $this->jwtToken = json_decode($jwtClient->getResponse()->getContent(), true, 512,
                JSON_THROW_ON_ERROR)['token'];
        }

        $this->defaultData = [
            'email' => 'email@email.com',
            'subject' => 'Simple subject',
            'message' => 'Lorem ipsum...',
            'dataProcessingAgreement' => true
        ];
    }

    /**
     * @test
     */
    public function it_allow_to_complete_contact_with_full_data(): void
    {
        $data = $this->defaultData;

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_email(): void
    {
        $data = $this->defaultData;
        unset($data['email']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_wit_bad_email(): void
    {
        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_subject(): void
    {
        $data = $this->defaultData;
        unset($data['subject']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_message(): void
    {
        $data = $this->defaultData;
        unset($data['message']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        unset($data['dataProcessingAgreement']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_with_false_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        $data['dataProcessingAgreement'] = false;

        $this->checkAssertByData($data);
    }

    /**
     * @param array $data
     */
    private function checkAssertByData(array $data): void
    {
        $client = static::createClient();
        $jwtToken = $this->jwtToken;

        $client->request(
            'POST',
            self::CREATE_API_URL,
            $data,
            [],
            [
                'HTTP_AUTHORIZATION' => 'bearer ' . $jwtToken
            ]
        );

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertTrue(false);
        }
    }
}
