<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 *
 */
class IndexController extends AbstractController
{
    /**
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }
}
