<?php

namespace App\Form\DataMapper;

use App\Entity\ShopColor;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopColorDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopColor $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        $parameterName = $parameters['name'];
        if ($parameterName) {
            if ($viewData->getId() === null) {
                $viewData = new ShopColor($parameterName, $parameters['enable']);
            } else {
                $viewData->setName($parameterName);
                $viewData->setEnable($parameters['enable']);
            }
        }
    }
}
