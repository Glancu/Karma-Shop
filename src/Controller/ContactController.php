<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ContactController
 *
 * @package App\Controller
 *
 * @Route("/api/contact")
 */
class ContactController
{
    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $form;

    /**
     * NewsletterController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->form = $formFactory;
    }

    /**
     * @Route("/create", name="add_contact", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function createContact(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;
        $contact = new Contact();

        $email = $request->get('email');
        $subject = $request->get('subject');
        $message = $request->get('message');

        $data = [
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        $contactForm = $form->create(ContactType::class, $contact);
        $contactForm->handleRequest($request);
        $contactForm->submit($data);

        $errors = $validator->validate($contact);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact->setEmail($email);
            $contact->setSubject($subject);
            $contact->setMessage($message);

            $em->persist($contact);
            $em->flush();

            $return = $serializer->serialize($contact, 'json');

            return new JsonResponse($return);
        }

        return new JsonResponse((string)$errors);
    }
}
