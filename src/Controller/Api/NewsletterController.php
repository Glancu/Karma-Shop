<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Newsletter;
use App\Form\Type\NewsletterType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class NewsletterController
 *
 * @package App\Controller
 *
 * @Route("/newsletter")
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
            'dataProcessingAgreement' => (bool)$request->request->get('dataProcessingAgreement'),
        ];

        if(!UserService::validateEmail($data['email'])) {
            $errorsList = ['error' => true, 'message' => 'Email is not valid.'];

            return new JsonResponse($errorsList, 400);
        }

        $newsletter = new Newsletter($data['email'], $data['dataProcessingAgreement'], $data['name']);

        $newsletterForm = $form->create(NewsletterType::class, $newsletter);
        $newsletterForm->handleRequest($request);
        $newsletterForm->submit($data);

        $errors = $validator->validate($newsletter);
        if($errors->count() === 0) {
            $newsletterObj = $em->getRepository('App:Newsletter')->findOneBy([
                'email' => $data['email']
            ]);
            if($newsletterObj) {
                $errorsList = ['error' => true, 'message' => 'User is saved with this email.'];

                return new JsonResponse($errorsList, 400);
            }
        }

        if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $em->persist($newsletter);
            $em->flush();

            return new JsonResponse($newsletter, 201);
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
