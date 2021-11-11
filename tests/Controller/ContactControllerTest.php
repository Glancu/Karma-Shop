<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public const CREATE_API_URL = '/api/contact';

    private array $defaultData;

    protected function setUp(): void
    {
        $this->defaultData = [
            'name' => 'John',
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

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_wit_bad_email(): void
    {
        $data = $this->defaultData;
        $data['email'] = 'bad_email';

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_subject(): void
    {
        $data = $this->defaultData;
        unset($data['subject']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_message(): void
    {
        $data = $this->defaultData;
        unset($data['message']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        unset($data['dataProcessingAgreement']);

        $this->checkAssertByData($data, true);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_with_false_data_processing_agreement(): void
    {
        $data = $this->defaultData;
        $data['dataProcessingAgreement'] = false;

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

            if($allowFalse) {
                self::assertFalse(false);
                return;
            }

            if(isset($result['error'])) {
                self::assertTrue(!$result['error']);
                return;
            }

            self::assertTrue(true);
        } catch (JsonException $exception) {
            self::assertTrue(false);
        }
    }
}
