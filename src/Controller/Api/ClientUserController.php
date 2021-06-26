<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ClientUser;
use App\Form\Type\CreateClientUserFormType;
use App\Service\SerializeDataResponse;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @Route("/create", name="user_create", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param SerializeDataResponse $serializeDataResponse
     *
     * @return Response
     */
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        SerializeDataResponse $serializeDataResponse
    ): Response {
        $em = $this->entityManager;
        $form = $this->form;
        $clientUser = new ClientUser();

        $data = [
            'firstName' => $request->request->get('firstName'),
            'lastName' => $request->request->get('lastName'),
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'phoneNumber' => $request->request->get('phoneNumber'),
            'postalCode' => $request->request->get('postalCode'),
            'city' => $request->request->get('city'),
            'country' => $request->request->get('country'),
            'street' => $request->request->get('street')
        ];

        if(isset($data['password'])) {
            $encodedPassword = $userPasswordEncoder->encodePassword($clientUser, $data['password']);
            $data['password'] = $encodedPassword;
        }

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

                $return = $serializer->serialize($errorsList, 'json');

                return new Response($return);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($clientUser);
            $em->flush();

            $jsonData = $serializeDataResponse->getClientUserData($clientUser);

            return new Response($jsonData);
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

    /**
     * @Route("/validate_token", name="user_validate_token")
     *
     * @param Request $request
     * @param JWTEncoderInterface $encoder
     *
     * @return JsonResponse
     */
    public function validateTokenAction(Request $request, JWTEncoderInterface $encoder): JsonResponse
    {
        $return = ['success' => false];

        $data = [
            'token' => $request->request->get('token')
        ];
        if(!$data || !isset($data['token'])) {
            return new JsonResponse($return);
        }

        try {
            $token = $data['token'];

            $encoderData = $encoder->decode($token);
            if($encoderData) {
                // Timezone is getting from php.ini
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
        } catch (Exception $e) {
            throw new BadCredentialsException($e->getMessage(), 0, $e);
        }

        return new JsonResponse($return);
    }
}
