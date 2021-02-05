<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\Type\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
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
     * @return Response
     */
    public function createNewsletter(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $em = $this->entityManager;
        $form = $this->form;
        $newsletter = new Newsletter();

        $data = [
            'name' => $request->request->get('name'),
            'email' => $request->request->get('email'),
            'dataProcessingAgreement' => $request->request->get('dataProcessingAgreement'),
        ];

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

                $return = $serializer->serialize($errorsList, 'json');

                return new Response($return);
            }
        }

        if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $em->persist($newsletter);
            $em->flush();

            $createdObjectJson = $serializer->serialize($newsletter, 'json');

            return new Response($createdObjectJson);
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
}
