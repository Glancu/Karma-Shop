<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Entity\EmailTemplate;
use App\Form\Type\ContactType;
use App\Service\MailerService;
use App\Service\RedisCacheService;
use App\Service\RequestService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ContactController
 *
 * @package App\Controller
 *
 * @Route("/contact")
 *
 * @OA\Tag(name="Contact")
 */
class ContactController
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $form;
    private RedisCacheService $redisCacheService;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        RedisCacheService $redisCacheService
    ) {
        $this->entityManager = $entityManager;
        $this->form = $formFactory;
        $this->redisCacheService = $redisCacheService;
    }

    /**
     * @Route("/create", name="add_contact", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to send contact form",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"name", "email", "subject", "message", "dataProcessingAgreement"},
     *             @OA\Property(
     *                 property="name",
     *                 description="name",
     *                 type="string",
     *                 example="John"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="email",
     *                 type="string",
     *                 example="user@email.com"
     *             ),
     *             @OA\Property(
     *                 property="subject",
     *                 description="subject",
     *                 type="string",
     *                 example="Simple subject"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 description="message",
     *                 type="string",
     *                 example="This is comment message"
     *             ),
     *             @OA\Property(
     *                 property="dataProcessingAgreement",
     *                 description="Accept data terms",
     *                 type="boolean",
     *                 example=true
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Request contact form was sent",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error":false,"uuid":"ca1a23c5-c638-42a6-99d5-5bc54ea0a26c"}
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Email is not valid."}
     *     )
     * )
     *
     * @Security()
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerService $mailerService
     * @param RequestService $requestService
     *
     * @return JsonResponse
     * @throws JsonException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function createContact(
        Request $request,
        ValidatorInterface $validator,
        MailerService $mailerService,
        RequestService $requestService
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $requiredDataFromContent = [
            'name',
            'email',
            'subject',
            'message',
            'dataProcessingAgreement'
        ];

        $data = $requestService->validRequestContentAndGetData($request->getContent(), $requiredDataFromContent);
        if ($data instanceof JsonResponse) {
            return $data;
        }

        if (!UserService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        if ($data['dataProcessingAgreement'] !== true) {
            return new JsonResponse(['error' => true, 'message' => 'Accept terms before sent contact form.'], 400);
        }

        $contact = new Contact($data['name'], $data['email'], $data['subject'], $data['message'],
            $data['dataProcessingAgreement']);

        $contactForm = $form->create(ContactType::class, $contact);

        $errors = $validator->validate($contact);
        if ($errors->count() === 0) {
            $contactForm->submit($data);
            $contactForm->handleRequest($request);

            if ($contactForm->isSubmitted() && $contactForm->isValid()) {
                $em->persist($contact);
                $em->flush();


                $emailTemplateType = EmailTemplate::TYPE_NEW_CONTACT_TO_ADMIN;
                /**
                 * @var EmailTemplate $emailTemplate
                 */
                $emailTemplate = $this->redisCacheService->getAndSaveIfNotExist(
                    'emailTemplate.'.$emailTemplateType,
                    EmailTemplate::class,
                    'findByType',
                    $emailTemplateType
                );
                if ($emailTemplate) {
                    $mailerService->sendMail(
                        $emailTemplate->getSubject(),
                        $emailTemplate->getMessage(),
                        $mailerService->getAdminEmail()
                    );
                }

                return new JsonResponse(['error' => false, 'uuid' => $contact->getUuid()], 201);
            }
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $contactForm->getErrors(true)->count();
        if ($errorsCount > 0 && $contactForm->getErrors(true)[0]->getOrigin()) {
            $errorsList['message'] = $contactForm->getErrors(true)[0]->getOrigin()
                                                                     ->getName() . ' - ' . $contactForm->getErrors(true)[0]->getMessage();
        } elseif ($errors->count() > 0) {
            $errorsList['message'] = $errors->get(0)->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
