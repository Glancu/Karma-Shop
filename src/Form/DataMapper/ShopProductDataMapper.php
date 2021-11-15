<?php

namespace App\Form\DataMapper;

use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use App\Entity\ShopProductSpecification;
use App\Entity\SonataMediaMedia;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopProductDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopProduct $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new ShopProduct(
                $parameters['name'] ?? '',
                $parameters['priceNet'] ?? 0,
                $parameters['priceGross'] ?? 0,
                $parameters['quantity'] ?? 1,
                $parameters['description'] ?? '',
                $parameters['shopBrand'],
                $parameters['shopCategory'],
                $parameters['shopProductSpecifications'],
                $parameters['images'],
                $parameters['enable'] ?? true,
                $parameters['shopColors'] ?? []
            );
        } else {
            $viewData->setName($parameters['name']);
            $viewData->setPriceNet($parameters['priceNet']);
            $viewData->setPriceGross($parameters['priceGross']);
            $viewData->setQuantity($parameters['quantity']);
            $viewData->setDescription($parameters['description']);
            $viewData->setShopBrand($parameters['shopBrand']);
            $viewData->setShopCategory($parameters['shopCategory']);
            $viewData->setEnable($parameters['enable']);

            if (is_array($parameters['shopProductSpecifications'])) {
                foreach ($parameters['shopProductSpecifications'] as $productSpecification) {
                    $viewData->addShopProductSpecification($productSpecification);
                }
            } elseif ($parameters['shopProductSpecifications'] instanceof ShopProductSpecification) {
                $viewData->addShopProductSpecification($parameters['shopProductSpecifications']);
            }

            if (is_array($parameters['images'])) {
                foreach ($parameters['images'] as $image) {
                    $viewData->addImage($image);
                }
            } elseif ($parameters['images'] instanceof SonataMediaMedia) {
                $viewData->addImage($parameters['images']);
            }

            if (is_array($parameters['shopColors'])) {
                foreach ($parameters['shopColors'] as $color) {
                    $viewData->addShopColor($color);
                }
            } elseif ($parameters['shopColors'] instanceof ShopColor) {
                $viewData->addShopColor($parameters['shopColors']);
            }
        }
    }
}
