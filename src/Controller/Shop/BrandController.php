<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BrandController
 *
 * @Route("/api/brands")
 *
 * @package App\Controller\Shop
 */
class BrandController
{
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * BrandController constructor.
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
     * @Route("/list", name="shop_brands_list", methods={"GET"})
     *
     * @return Response
     */
    public function getBrandsList(): Response
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;

        $items = $em->getRepository('App:ShopBrand')->findBy(
            ['enable' => true],
            ['id' => 'DESC']);

        $response = new Response($serializer->getBrandsList($items));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
