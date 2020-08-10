<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContactControllerTest extends WebTestCase {
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
    public function it_allow_to_complete_contact_with_full_data(): void {
        $validator = $this->validator;

        if($validator) {
            $contact = new Contact();
            $contact->setEmail('email@email.com');
            $contact->setSubject('Simple subject');
            $contact->setMessage('Simple message');

            $errors = $validator->validate($contact);

            self::assertEquals(0, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_email(): void {
        $validator = $this->validator;

        if($validator) {
            $contact = new Contact();
            $contact->setSubject('Simple subject');
            $contact->setMessage('Simple message');

            $errors = $validator->validate($contact);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_wit_bad_email(): void {
        $validator = $this->validator;

        if($validator) {
            $contact = new Contact();
            $contact->setEmail('bad_email');
            $contact->setSubject('Simple subject');
            $contact->setMessage('Simple message');

            $errors = $validator->validate($contact);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_subject(): void {
        $validator = $this->validator;

        if($validator) {
            $contact = new Contact();
            $contact->setEmail('email@email.com');
            $contact->setMessage('Simple message');

            $errors = $validator->validate($contact);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_contact_without_message(): void {
        $validator = $this->validator;

        if($validator) {
            $contact = new Contact();
            $contact->setEmail('email@email.com');
            $contact->setSubject('Simple subject');

            $errors = $validator->validate($contact);

            self::assertEquals(1, $errors->count());
        } else {
            self::fail('Validator not set');
        }
    }
}
