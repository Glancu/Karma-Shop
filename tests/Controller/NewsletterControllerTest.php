<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Newsletter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NewsletterControllerTest extends WebTestCase
{
    private ?ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');

        self::ensureKernelShutdown();
    }

    /**
     * @test
     */
    public function it_allow_to_complete_newsletter_with_full_data(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setName('John');
            $newsletter->setEmail('email@email.com');
            $newsletter->setDataProcessingAgreement(true);

            $errors = $validator->validate($newsletter);

            self::assertEquals(0, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_allow_to_complete_newsletter_without_name(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setEmail('email@email.com');
            $newsletter->setDataProcessingAgreement(true);

            $errors = $validator->validate($newsletter);

            self::assertEquals(0, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_without_email(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setName('John');
            $newsletter->setDataProcessingAgreement(true);

            $errors = $validator->validate($newsletter);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_wit_bad_email(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setName('John');
            $newsletter->setEmail('bad_email');
            $newsletter->setDataProcessingAgreement(true);

            $errors = $validator->validate($newsletter);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_without_data_processing_agreement(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setName('John');
            $newsletter->setEmail('email@email.com');

            $errors = $validator->validate($newsletter);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_newsletter_with_false_data_processing_agreement(): void
    {
        $validator = $this->validator;

        if ($validator) {
            $newsletter = new Newsletter();
            $newsletter->setName('John');
            $newsletter->setEmail('email@email.com');
            $newsletter->setDataProcessingAgreement(false);

            $errors = $validator->validate($newsletter);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }
}
