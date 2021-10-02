<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Form\Type\CreateClientUserFormType;
use App\Service\SerializeDataResponse;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ClientUserController
 *
 * @package App\Controller
 *
 * @Route("/api/user")
 */
class ClientUserController
{
    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $form;

    /**
     * ClientUserController constructor.
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
     * Route("/create", name="user_create", methods={"POST"})
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializeDataResponse $serializeDataResponse
     * @param UserService $userService
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function createUser(
        Request $request,
        ValidatorInterface $validator,
        SerializeDataResponse $serializeDataResponse,
        UserService $userService
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $data = [
            'email' => htmlspecialchars((string)$request->request->get('email'), ENT_QUOTES),
            'password' => htmlspecialchars((string)$request->request->get('password'), ENT_QUOTES)
        ];

        if(!$userService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        $clientUser = $userService->createUser($data['email'], $data['password']);
        $data['password'] = $clientUser->getPassword();

        $form = $form->create(CreateClientUserFormType::class, $clientUser);
        $form->handleRequest($request);
        $form->submit($data);

        $errors = $validator->validate($clientUser);
        if($errors->count() === 0) {
            $clientUserObj = $em->getRepository('App:ClientUser')->findOneBy([
                'email' => $data['email']
            ]);
            if($clientUserObj) {
                $errorsList = ['error' => true, 'message' => 'User is register with this email.'];

                return new JsonResponse($errorsList, 400);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($clientUser);
            $em->flush();

            $jsonData = $serializeDataResponse->getClientUserData($clientUser);

            return JsonResponse::fromJsonString($jsonData, 201);
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

    /**
     * @Route("/validate_token", name="user_validate_token")
     *
     * @param Request $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function validateTokenAction(Request $request, UserService $userService): JsonResponse
    {
        $return = ['success' => false];

        $data = [
            'token' => htmlspecialchars((string)$request->request->get('token'), ENT_QUOTES)
        ];
        if(!$data || !isset($data['token'])) {
            return new JsonResponse($return);
        }

        $encoderData = $userService->decodeUserByJWTToken($data['token']);
        if($encoderData) {
            $dateTime = new DateTime();
            $currentDate = $dateTime->format('Y-m-d H:i:s');
            $dateStringTokenStart = $encoderData['iat'];

            $dateTokenStart = new DateTime("@$dateStringTokenStart");
            $dateFormatTokenStart = $dateTokenStart->format('Y-m-d H:i:s');

            $dateStringTokenExpire = $encoderData['exp'];
            $dateTokenExpire = new DateTime("@$dateStringTokenExpire");
            $dateFormatTokenExpire = $dateTokenExpire->format('Y-m-d H:i:s');

            if($currentDate >= $dateFormatTokenStart && $currentDate <= $dateFormatTokenExpire) {
                $return = ['success' => true];
                return new JsonResponse($return);
            }
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/get-email", name="user_email_by_token")
     *
     * @param Request $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function getEmailByTokenAction(Request $request, UserService $userService): JsonResponse
    {
        $return = ['success' => false];

        $data = [
            'token' => htmlspecialchars((string)$request->request->get('token'), ENT_QUOTES)
        ];
        if(!$data || !isset($data['token'])) {
            return new JsonResponse($return);
        }

        $encoderData = $userService->decodeUserByJWTToken($data['token']);
        if($encoderData) {
            $return = ['email' => $encoderData['email']];
            return new JsonResponse($return);
        }

        return new JsonResponse($return);
    }
}
