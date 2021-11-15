<?php

namespace App\Form\DataMapper;

use App\Entity\ShopProductSpecification;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopProductSpecificationDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopProductSpecification $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new ShopProductSpecification(
                $parameters['shopProductSpecificationType'],
                $parameters['value']
            );
        } else {
            $viewData->setShopProductSpecificationType($parameters['shopProductSpecificationType']);
            $viewData->setValue($parameters['value']);
        }
    }
}
