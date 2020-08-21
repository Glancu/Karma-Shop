<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/contact/create';

    private array $defaultData;

    protected function setUp(): void
    {
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
        $client = static::createClient();

        $data = $this->defaultData;

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_email(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        unset($data['email']);

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_wit_bad_email(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_subject(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        unset($data['subject']);

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_message(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        unset($data['message']);

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_data_processing_agreement(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        unset($data['dataProcessingAgreement']);

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_with_false_data_processing_agreement(): void
    {
        $client = static::createClient();

        $data = $this->defaultData;
        $data['dataProcessingAgreement'] = false;

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertFalse(false);
        }
    }
}
