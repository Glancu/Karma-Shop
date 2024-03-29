<?php
declare(strict_types=1);

namespace App\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AdminSecurityController
 *
 * @package App\FrontendBundle\Controller
 */
class AdminSecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", name="app_admin_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin_security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin/logout", name="app_admin_logout")
     *
     * @throws RuntimeException
     */
    public function logout(): void
    {
        throw new RuntimeException('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
