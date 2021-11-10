<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Newsletter;
use App\Form\Type\NewsletterType;
use App\Service\RequestService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class NewsletterController
 *
 * @package App\Controller
 *
 * @Route("/newsletter")
 *
 * @OA\Tag(name="Newsletter")
 */
class NewsletterController
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
     * @Route("/create", name="add_newsletter", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to submit to the newsletter",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             type="object",
     *             required={"email", "dataProcessingAgreement"},
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
     *                 property="dataProcessingAgreement",
     *                 description="Accept data terms",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Newsletter was created",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": false, "email": "email@email.com"}
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
     *
     * @return JsonResponse
     */
    public function createNewsletter(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $data = [
            'name' => htmlspecialchars((string)$request->request->get('name'), ENT_QUOTES),
            'email' => htmlspecialchars((string)$request->request->get('email'), ENT_QUOTES),
            'dataProcessingAgreement' => RequestService::isDataProcessingAgreementValid($request->request->get('dataProcessingAgreement'))
        ];

        if (!UserService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        if (!$data['dataProcessingAgreement']) {
            return new JsonResponse(['error' => true, 'message' => 'Accept terms before submit to newsletter.'], 400);
        }

        $newsletter = new Newsletter($data['email'], $data['dataProcessingAgreement'], $data['name']);

        $newsletterForm = $form->create(NewsletterType::class, $newsletter);
        $newsletterForm->handleRequest($request);
        $newsletterForm->submit($data);

        $errors = $validator->validate($newsletter);
        if ($errors->count() === 0) {
            $newsletterObj = $em->getRepository('App:Newsletter')->findOneBy([
                'email' => $data['email']
            ]);
            if ($newsletterObj) {
                $errorsList = ['error' => true, 'message' => 'User is saved with this email.'];

                return new JsonResponse($errorsList, 400);
            }
        }

        if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $em->persist($newsletter);
            $em->flush();

            return new JsonResponse(['error' => false, 'email' => $newsletter->getEmail()], 201);
        }

        $errorsList = ['error' => true, 'message' => []];

        $errorsCount = $newsletterForm->getErrors(true)->count();
        if ($errorsCount > 0) {
            $errorsList['message'] = $newsletterForm->getErrors(true)[0]->getOrigin()
                                                                        ->getName() . ' - ' . $newsletterForm->getErrors(true)[0]->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
