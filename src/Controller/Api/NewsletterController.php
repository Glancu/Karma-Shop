<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Newsletter;
use App\Form\Type\NewsletterType;
use App\Service\RedisCacheService;
use App\Service\RequestService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/create", name="add_newsletter", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to submit to the newsletter",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
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
     * @param RequestService $requestService
     *
     * @return JsonResponse
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public function createNewsletter(
        Request $request,
        ValidatorInterface $validator,
        RequestService $requestService
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $requiredDataFromContent = [
            'name',
            'email',
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

        if (!$data['dataProcessingAgreement']) {
            return new JsonResponse(['error' => true, 'message' => 'Accept terms before submit to newsletter.'], 400);
        }

        $newsletter = new Newsletter($data['email'], $data['dataProcessingAgreement'], $data['name'] ?? null);

        $newsletterForm = $form->create(NewsletterType::class, $newsletter);
        $newsletterForm->handleRequest($request);
        $newsletterForm->submit($data);

        $errors = $validator->validate($newsletter);
        if ($errors->count() === 0) {
            $newsletterObj = $this->redisCacheService->getAndSaveIfNotExist(
                'newsletter.'.str_replace('@', '', $data['email']),
                Newsletter::class,
                'findByEmail',
                $data['email']
            );
            if ($newsletterObj) {
                $errorsList = ['error' => true, 'message' => 'Email already exist.'];

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
        if ($errorsCount > 0 && $newsletterForm->getErrors(true)[0]->getOrigin()) {
            $errorsList['message'] = $newsletterForm->getErrors(true)[0]->getOrigin()
                                                                        ->getName() . ' - ' . $newsletterForm->getErrors(true)[0]->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }
}
