<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 *
 * @Route("/api/categories")
 *
 * @package App\Controller\Shop
 */
class CategoryController
{
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * CategoryController constructor.
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
     * @Route("/list", name="shop_categories_list", methods={"GET"})
     *
     * @return Response
     */
    public function getCategoriesList(): Response
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;

        $items = $em->getRepository('App:ShopCategory')->findBy(
            ['enable' => true],
            ['id' => 'DESC']);

        $response = new Response($serializer->getCategoriesList($items));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
