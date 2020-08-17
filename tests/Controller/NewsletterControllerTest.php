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
        $client = static::createClient();

        $data = $this->defaultData;

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertSame(strpos($client->getResponse()->getContent(), '"{'), 0);
    }

    /**
     * @test
     */
    public function it_allow_to_complete_newsletter_without_name(): void
    {
        $client = static::createClient();

        $defaultData = $this->defaultData;
        $data = [
            'email' => $defaultData['email'],
            'dataProcessingAgreement' => $defaultData['dataProcessingAgreement']
        ];

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertSame(strpos($client->getResponse()->getContent(), '"{'), 0);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_without_email(): void
    {
        $client = static::createClient();

        $defaultData = $this->defaultData;
        $data = [
            'name' => $defaultData['name'],
            'dataProcessingAgreement' => $defaultData['dataProcessingAgreement']
        ];

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertNotSame(strpos($client->getResponse()->getContent(), '"{'), 0);
    }

    /**
     * @test
     * @throws JsonException
     */
    public function it_does_not_allow_to_complete_newsletter_wit_bad_email(): void
    {
        $client = static::createClient();

        $defaultData = $this->defaultData;
        $data = [
            'name' => $defaultData['name'],
            'email' => 'bad_email',
            'dataProcessingAgreement' => $defaultData['dataProcessingAgreement']
        ];

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertNotFalse(strpos(json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
            'Object(App\\Entity\\Newsletter).email'));
    }

    /**
     * @test
     * @throws JsonException
     */
    public function it_does_not_allow_to_complete_newsletter_without_data_processing_agreement(): void
    {
        $client = static::createClient();

        $defaultData = $this->defaultData;
        $data = [
            'name' => $defaultData['name'],
            'email' => $defaultData['email']
        ];

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertNotFalse(strpos(json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
            'Object(App\\Entity\\Newsletter).dataProcessingAgreement'));
    }

    /**
     * @test
     *
     * @throws JsonException
     */
    public function it_does_not_allow_to_complete_newsletter_with_false_data_processing_agreement(): void
    {
        $client = static::createClient();

        $defaultData = $this->defaultData;
        $data = [
            'name' => $defaultData['name'],
            'email' => $defaultData['email'],
            'dataProcessingAgreement' => false
        ];

        $client->request('POST', self::CREATE_API_URL, $data);

        self::assertNotFalse(strpos(json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
            'Object(App\\Entity\\Newsletter).dataProcessingAgreement'));
    }
}
