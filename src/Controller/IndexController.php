<?php
declare(strict_types=1);

namespace App\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 *
 * @package App\Controller
 */
class IndexController extends AbstractController {
    /**
     * @Route("/{reactRouting}", name="homepage", defaults={"reactRouting"=".+"})
     *
     * @return Response
     */
    public function index(): Response {
        return $this->render('Index/index.html.twig');
    }

    /**
     * @Route("/api/users", name="users")
     *
     * @return JsonResponse
     * @throws JsonException
     */
    public function getUsers(): Response {
        $users = [
            [
                'id' => 1,
                'name' => 'Olususi Oluyemi',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation',
                'imageURL' => 'https://randomuser.me/api/portraits/women/50.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Camila Terry',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation',
                'imageURL' => 'https://randomuser.me/api/portraits/men/42.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Joel Williamson',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation',
                'imageURL' => 'https://randomuser.me/api/portraits/women/67.jpg'
            ],
            [
                'id' => 4,
                'name' => 'Deann Payne',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation',
                'imageURL' => 'https://randomuser.me/api/portraits/women/50.jpg'
            ],
            [
                'id' => 5,
                'name' => 'Donald Perkins',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation',
                'imageURL' => 'https://randomuser.me/api/portraits/men/89.jpg'
            ]
        ];

        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode($users, JSON_THROW_ON_ERROR));

        return $response;
    }
}
