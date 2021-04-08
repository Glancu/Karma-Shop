<?php
declare(strict_types=1);

namespace App\Controller\Shop;

use App\Service\SerializeDataResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 *
 * @package App\Controller\Shop
 *
 * @Route("/api/products")
 */
class ProductController
{
    private EntityManagerInterface $entityManager;
    private SerializeDataResponse $serializeDataResponse;

    /**
     * ProductController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SerializeDataResponse $serializeDataResponse
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SerializeDataResponse $serializeDataResponse
    ) {
        $this->entityManager = $entityManager;
        $this->serializeDataResponse = $serializeDataResponse;
    }

    /**
     * @Route("/list", name="shop_products_list", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getProductsList(Request $request): Response
    {
        $em = $this->entityManager;
        $serializer = $this->serializeDataResponse;

        $limit = $request->query->get('limit') ?: 12;
        $offset = $request->query->get('offset') ?: 0;
        $sortBy = $request->query->get('sortBy') ?: null;
        if($sortBy === 'price') {
            $sortBy = 'priceNet';
        }

        $sortOrder = $request->query->get('sortOrder') ?: 'DESC';

        $countProducts = $em->getRepository('App:ShopProduct')->getCountEnableProducts();

        $products = $em->getRepository('App:ShopProduct')
                       ->getProductsWithLimitAndOffset($limit, $offset, $sortBy, $sortOrder);

        $return = $serializer->getProductsData($products, $countProducts);

        if($countProducts === 0 && !$products) {
            $return = json_encode(['errorMessage' => 'Products was not found.']);
        }

        $response = new Response($return);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
