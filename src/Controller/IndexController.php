<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class IndexController
 *
 * @package App\Controller
 */
class IndexController extends AbstractController {
    public function index() {
        return $this->render('Index/index.html.twig');
    }
}