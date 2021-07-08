<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
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
     * @return Response
     */
    public function createContact(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $em = $this->entityManager;
        $form = $this->form;

        $data = [
            'name' => $request->request->get('name'),
            'email' => $request->request->get('email'),
            'subject' => $request->request->get('subject'),
            'message' => $request->request->get('message'),
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement')
        ];

        $contact = new Contact($data['name'], $data['email'], $data['subject'], $data['message'], $data['dataProcessingAgreement']);

        $contactForm = $form->create(ContactType::class, $contact);
        $contactForm->handleRequest($request);
        $contactForm->submit($data);

        $errors = $validator->validate($contact);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $em->persist($contact);
            $em->flush();

            $return = $serializer->serialize($contact, 'json');

            return new Response($return);
        }

        $errorsList = ['error' => true, 'message' => []];

        /**
         * @var ConstraintViolation $error
         */
        foreach($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        $return = $serializer->serialize($errorsList, 'json');

        return new Response($return);
    }
}
