<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\Type\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class NewsletterController
 *
 * @package App\Controller
 *
 * @Route("/api/newsletter")
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
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function createNewsletter(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $em = $this->entityManager;
        $form = $this->form;
        $newsletter = new Newsletter();

        $name = $request->get('name') ?? '';
        $email = $request->get('email');
        $dataProcessingAgreement = (bool)$request->get('dataProcessingAgreement');

        $data = [
            'name' => $name,
            'email' => $email,
            'dataProcessingAgreement' => $dataProcessingAgreement
        ];

        $newsletterForm = $form->create(NewsletterType::class, $newsletter);
        $newsletterForm->handleRequest($request);
        $newsletterForm->submit($data);

        $errors = $validator->validate($newsletter);

        if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $newsletter->setName($name);
            $newsletter->setEmail($email);
            $newsletter->setDataProcessingAgreement($dataProcessingAgreement);

            $em->persist($newsletter);
            $em->flush();

            $createdObjectJson = $serializer->serialize($newsletter, 'json');

            return new JsonResponse($createdObjectJson);
        }

        return new JsonResponse((string)$errors);
    }
}
