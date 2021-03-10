<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ColorController
 *
 * @Route("/api/colors")
 *
 * @package App\Controller\Shop
 */
class ColorController
{
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * ColorController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SerializeDataResponse $serializeDataResponse
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializeDataResponse $serializeDataResponse
    )
    {
        $this->entityManager = $entityManager;
        $this->serializeDataResponse = $serializeDataResponse;
    }

    /**
     * @Route("/list", name="shop_colors_list", methods={"GET"})
     *
     * @return Response
     */
    public function getCategoriesList(): Response
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;

        $items = $em->getRepository('App:ShopColor')->findBy(
            ['enable' => true],
            ['id' => 'DESC']);

        $response = new Response($serializer->getColorsList($items));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
