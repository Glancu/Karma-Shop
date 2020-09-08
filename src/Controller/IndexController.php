<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 *
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/{reactRouting}", name="homepage", defaults={"reactRouting"=".+"}, requirements={"reactRouting"=".+"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('Index/index.html.twig');
    }
}
