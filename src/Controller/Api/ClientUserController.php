<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ClientUser;
use App\Form\Type\ClientUserChangePasswordType;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ClientUserController
 *
 * @package App\Controller
 *
 * @Route("/user")
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

        if (!$userService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        $clientUser = $userService->createUser($data['email'], $data['password']);
        $data['password'] = $clientUser->getPassword();

        $form = $form->create(CreateClientUserFormType::class, $clientUser);
        $form->handleRequest($request);
        $form->submit($data);

        $errors = $validator->validate($clientUser);
        if ($errors->count() === 0) {
            $clientUserObj = $em->getRepository('App:ClientUser')->findOneBy([
                'email' => $data['email']
            ]);
            if ($clientUserObj) {
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
        foreach ($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }

    /**
     * @Route("/validate_token", name="user_validate_token", methods={"GET"})
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
        if (!$data || !isset($data['token'])) {
            return new JsonResponse($return);
        }

        $encoderData = $userService->decodeUserByJWTToken($data['token']);
        if ($encoderData) {
            /**
             * @var ClientUser $clientUser
             */
            $clientUser = $this->entityManager->getRepository('App:ClientUser')->findOneBy([
                'email' => $encoderData['email']
            ]);
            if (!$clientUser) {
                $return['message'] = 'User was not found';

                return new JsonResponse($return, 404);
            }

            $dateTime = new DateTime();
            $currentDate = $dateTime->format('Y-m-d H:i:s');
            $dateStringTokenStart = $encoderData['iat'];

            $dateTokenStart = new DateTime("@$dateStringTokenStart");
            $dateFormatTokenStart = $dateTokenStart->format('Y-m-d H:i:s');

            $dateStringTokenExpire = $encoderData['exp'];
            $dateTokenExpire = new DateTime("@$dateStringTokenExpire");
            $dateFormatTokenExpire = $dateTokenExpire->format('Y-m-d H:i:s');

            if ($clientUser->getPasswordChangedAt() && ($clientUser->getPasswordChangedAt()
                                                                   ->format('Y-m-d H:i:s') > $dateFormatTokenStart)) {
                return new JsonResponse(['success' => false, 'message' => 'Token has expired.'], 401);
            }

            if ($currentDate >= $dateFormatTokenStart && $currentDate <= $dateFormatTokenExpire) {
                return new JsonResponse(['success' => true]);
            }
        }

        $return['message'] = 'Token has expired.';

        return new JsonResponse($return, 401);
    }

    /**
     * @Route("/get-email", name="user_email_by_token", methods={"GET"})
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
        if (!$data || !isset($data['token'])) {
            return new JsonResponse($return);
        }

        $encoderData = $userService->decodeUserByJWTToken($data['token']);
        if ($encoderData) {
            $return = ['email' => $encoderData['email']];
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/change-password/{uuid}", name="user_change_password", methods={"PATCH"})
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializeDataResponse $serializeDataResponse
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UserInterface $userInterface
     *
     * @return JsonResponse
     */
    public function changePasswordAction(
        Request $request,
        ValidatorInterface $validator,
        SerializeDataResponse $serializeDataResponse,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserInterface $userInterface
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;

        $data = json_decode($request->getContent(), true);

        $clientEmail = $userInterface->getEmail();
        if (!$clientEmail) {
            return new JsonResponse(['error' => true, 'message' => 'User was not found'], 400);
        }

        /**
         * @var ClientUser $clientUser
         */
        $clientUser = $em->getRepository('App:ClientUser')->findByEmail($clientEmail);
        if (!$clientUser) {
            return new JsonResponse(['error' => true, 'message' => 'User was not found.'], 400);
        }

        $form = $form->create(ClientUserChangePasswordType::class);
        $form->handleRequest($request);
        $form->submit($data, false);

        $errors = $validator->validate($clientUser);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$userPasswordEncoder->isPasswordValid($clientUser, $data['oldPassword'])) {
                return new JsonResponse(['error' => true, 'message' => 'Old password is not valid'], 400);
            }

            if ($data['newPassword'] !== $data['newPasswordRepeat']) {
                return new JsonResponse(['error' => true, 'message' => 'New passwords do not match'], 400);
            }

            $clientUser->setPasswordChangedAt(new DateTime('now'));

            $newPasswordHashed = $userPasswordEncoder->encodePassword($clientUser, $data['newPassword']);
            $clientUser->setPassword($newPasswordHashed);

            $em->persist($clientUser);
            $em->flush();

            $jsonData = $serializeDataResponse->getClientUserData($clientUser);

            return JsonResponse::fromJsonString($jsonData, 200);
        }

        $errorsList = ['error' => true, 'message' => []];

        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $errorsList['message'][$error->getPropertyPath()] = $error->getMessage();
        }

        return new JsonResponse($errorsList, 400);
    }

    /**
     * @Route("/data", name="user_data", methods={"GET"})
     *
     * @param UserInterface $userInterface
     *
     * @return JsonResponse
     */
    public function userDataAction(UserInterface $userInterface): JsonResponse
    {
        $em = $this->entityManager;

        $userEmail = $userInterface->getEmail();

        /**
         * @var ClientUser $clientUser
         */
        $clientUser = $em->getRepository('App:ClientUser')->findByEmail($userEmail);
        if (!$clientUser) {
            return new JsonResponse(['error' => true, 'message' => 'User was not found'], 400);
        }

        $data = [
            'uuid' => $clientUser->getUuid(),
            'email' => $clientUser->getEmail()
        ];

        return new JsonResponse($data);
    }
}
