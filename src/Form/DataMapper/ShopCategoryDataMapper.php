<?php

namespace App\Form\DataMapper;

use App\Entity\ShopCategory;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopCategoryDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopCategory $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        $parameterTitle = $parameters['title'];
        if($parameterTitle) {
            if ($viewData->getId() === null) {
                $viewData = new ShopCategory($parameterTitle, $parameters['enable']);
            } else {
                $viewData->setTitle($parameterTitle);
                $viewData->setEnable($parameters['enable']);
            }
        }
    }
}
