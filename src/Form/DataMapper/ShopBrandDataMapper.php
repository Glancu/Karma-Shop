<?php

namespace App\Form\DataMapper;

use App\Entity\ShopBrand;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopBrandDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopBrand $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        $parameterTitle = $parameters['title'];
        if($parameterTitle) {
            if ($viewData->getId() === null) {
                $viewData = new ShopBrand($parameterTitle, $parameters['enable']);
            } else {
                $viewData->setTitle($parameterTitle);
                $viewData->setEnable($parameters['enable']);
            }
        }
    }
}
