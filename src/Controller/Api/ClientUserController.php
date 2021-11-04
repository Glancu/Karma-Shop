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
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * Class ClientUserController
 *
 * @package App\Controller
 *
 * @Route("/user")
 *
 * @OA\Tag(name="User")
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
     * @Route("/create", name="user_create", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass data to create an user",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 description="email",
     *                 type="string",
     *                 example="user@email.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="password",
     *                 type="string",
     *                 example="userpassword"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="User was created",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"email": "user@email.com", "createdAt": "2021-11-04T07:44:07+00:00", "uuid": "943ca7f7-424a-4a94-825b-8c87c3f9177f"}
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
     * @param SerializeDataResponse $serializeDataResponse
     * @param UserService $userService
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function createUserAction(
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

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($clientUser);
            $em->flush();

            $jsonData = $serializeDataResponse->getClientUserData($clientUser);

            return JsonResponse::fromJsonString($jsonData, 201);
        }

        $errors = $validator->validate($clientUser);

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
     * @Route("/validate_token", name="user_validate_token", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Pass token to validate",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             type="object",
     *             required={"token"},
     *             @OA\Property(
     *                 property="token",
     *                 description="token",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MzYwMDc0NDQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJlbWFpbCI6ImVtQGVtLnBsIiwiaWUiOnsiZGF0ZSI6IjIwMjEtMTEtMDQgMDY6MzA6NDQuNjgyNzUyIiwidGltZXpvbmVfdHlwZSI6MywidGltZXpvbmUiOiJVVEMifSwiZXhwIjoxNjM2MDExMDQ0fQ.zWmVkYnNOxx7GL7J39DGBoKatTnzsO2_27Yl7p6JlwT75Z6LD0U8nfRSCWNf3WLvCq52lmv0YbmZk3en9Zn5EQ3NaJYVHEw-hxKE5eqQg9SEwpfiWnCocSRaQKyyN4zD-i-DOwgSmRe_mXZAFjqxko3iDDKO70-QkKvryzontCnlQkM-9fu4wHtBjK4uIPrFI60PBvh68WZWyUPYDgrDUu6Z01QZRIGJ1hQHf04_lJmZxHeWYbQ7OBZGGyqSnkzrS_Hc01yCpLQipAW9M96QTSZPp3R3aPZznRHYtaapISpWMc6kbr1w8hOQwKZj2B6X27-6m_8g3GeONklWBFTInBD613HMgzHrClRTdTfj_Ezrcnp1XyIz8fehvWeMrMJoqkYKS2e_XB60QIxBiLlw6-vZF-Ym0XcW4jvFLmkWSCFOOV37NBM_K7AIdZ8to1YfTHfByCWwWMC3jvt-T6UAOYOK6Eu5rMd4rH2kn2XDLlW7ziqim1v_KBuqF02bgb6clR9imzBsMQ46maPj5MwSNAvC5XURA6EH6kJPYxhK2cmnAModzLt7pm6H0udI6Jn03bwu_H3UK2ZWum72pCiKN2z56lTSjrYrUz2tLn2azVim8GRejoqMh-2u0HRPmL8K2w1l-AdCEJLAtCzWn6v139KSIWDdkF8NfLi7dQTUzJM"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"success": true}
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized. JWT Token has expired",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"success": false, "message": "Token has expired."}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not Found. JWT Token was not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"success": false, "message": "Token was not found."}
     *     )
     * )
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

            if ($clientUser->getPasswordChangedAt() &&
                ($clientUser->getPasswordChangedAt()->format('Y-m-d H:i:s') > $dateFormatTokenStart)
            ) {
                return new JsonResponse(['success' => false, 'message' => 'Token has expired.'], 401);
            }

            if ($currentDate >= $dateFormatTokenStart && $currentDate <= $dateFormatTokenExpire) {
                return new JsonResponse(['success' => true]);
            }
        }

        $return['message'] = 'Token was not found.';

        return new JsonResponse($return, 404);
    }

    /**
     * @Route("/change-password/{uuid}", name="user_change_password", methods={"PATCH"})
     *
     * @OA\Parameter(
     *     name="uuid",
     *     in="path",
     *     description="User UUID",
     *     required=true,
     *     @OA\Schema(type="string", example="f665e032-a799-4ae7-ba41-530e1889a6e7")
     * )
     *
     * @OA\RequestBody(
     *     description="Change user password",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="oldPassword",
     *                 description="Old password",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="newPassword",
     *                 description="New password",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="newPasswordRepeat",
     *                 description="Repeat of new password",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Change password",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"email": "string", "createdAt": "2021-11-03T10:45:21.311Z", "uuid": "string"}
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request. Please check message of response",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "Old password is not valid"}
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized. JWT Token not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"code": 401, "message": "JWT Token not found"}
     *     )
     * )
     *
     * @Security(name="Bearer")
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

        if(!$data || !isset($data['oldPassword'], $data['newPassword'], $data['newPasswordRepeat'])) {
            return new JsonResponse(['error' => true, 'message' => 'Not found data to execute'], 400);
        }

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
     * @OA\Response(
     *     response=200,
     *     description="Returns the data of an user",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(property="uuid", type="string"),
     *        @OA\Property(property="email", type="string")
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized. JWT Token not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"code": 401, "message": "JWT Token not found"}
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found. User was not found",
     *     @OA\JsonContent(
     *        type="json",
     *        example={"error": true, "message": "User was not found."}
     *     )
     * )
     *
     * @Security(name="Bearer")
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
            return new JsonResponse(['error' => true, 'message' => 'User was not found.'], 404);
        }

        $data = [
            'uuid' => $clientUser->getUuid(),
            'email' => $clientUser->getEmail()
        ];

        return new JsonResponse($data);
    }
}
