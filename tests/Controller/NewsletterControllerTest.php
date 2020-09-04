<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NewsletterControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/newsletter/create';

    private array $defaultData;

    protected function setUp(): void
    {
        $this->defaultData = [
            'name' => 'John',
            'email' => 'email@email.com',
            'dataProcessingAgreement' => true
        ];
    }

    /**
     * @test
     */
    public function it_allow_to_complete_newsletter_with_full_data(): void
    {
        $data = $this->defaultData;

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_allow_to_complete_newsletter_without_name(): void
    {
        $data = $this->defaultData;
        unset($data['name']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_without_email(): void
    {
        $data = $this->defaultData;
        unset($data['email']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_wit_bad_email(): void
    {
        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_without_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        unset($data['dataProcessingAgreement']);

        $this->checkAssertByData($data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_with_false_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        $data['dataProcessingAgreement'] = false;

        $this->checkAssertByData($data);
    }

    /**
     * @param $data
     */
    private function checkAssertByData($data): void
    {
        $client = static::createClient();

        $client->request('POST', self::CREATE_API_URL, $data);

        try {
            $result = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertTrue((bool)$result);
        } catch (JsonException $exception) {
            self::assertTrue(false);
        }
    }
}
