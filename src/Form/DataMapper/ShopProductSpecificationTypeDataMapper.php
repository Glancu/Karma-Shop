<?php

namespace App\Form\DataMapper;

use App\Entity\ShopProductSpecificationType;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopProductSpecificationTypeDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopProductSpecificationType $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new ShopProductSpecificationType($parameters['name']);
        } else {
            $viewData->setName($parameters['name']);
        }
    }
}
