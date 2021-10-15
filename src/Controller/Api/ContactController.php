<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Entity\EmailTemplate;
use App\Form\Type\ContactType;
use App\Service\MailerService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * ContactController constructor.
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
     * @param ValidatorInterface $validator
     * @param MailerService $mailerService
     *
     * @return JsonResponse
     */
    public function createContact(
        Request $request,
        ValidatorInterface $validator,
        MailerService $mailerService
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $data = [
            'name' => htmlspecialchars((string)$request->request->get('name'), ENT_QUOTES),
            'email' => htmlspecialchars((string)$request->request->get('email'), ENT_QUOTES),
            'subject' => htmlspecialchars((string)$request->request->get('subject'), ENT_QUOTES),
            'message' => htmlspecialchars((string)$request->request->get('message'), ENT_QUOTES),
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement')
        ];

        if(!UserService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        $contact = new Contact($data['name'], $data['email'], $data['subject'], $data['message'], $data['dataProcessingAgreement']);

        $errors = $validator->validate($contact);
        if($errors->count() === 0) {
            $contactForm = $form->create(ContactType::class, $contact);
            $contactForm->handleRequest($request);
            $contactForm->submit($data);

            if ($contactForm->isSubmitted() && $contactForm->isValid()) {
                $em->persist($contact);
                $em->flush();

                /**
                 * @var EmailTemplate $emailTemplate
                 */
                $emailTemplate = $em->getRepository('App:EmailTemplate')->findByType(EmailTemplate::TYPE_NEW_CONTACT_TO_ADMIN);
                if($emailTemplate) {
                    $mailerService->sendMail(
                        $emailTemplate->getSubject(),
                        $emailTemplate->getMessage(),
                        $mailerService->getAdminEmail()
                    );
                }

                return new JsonResponse($contact, 201);
            }
        }

        $errorsList = ['error' => true, 'message' => []];

        /**
         * @var ConstraintViolation $error
         */
        foreach($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
