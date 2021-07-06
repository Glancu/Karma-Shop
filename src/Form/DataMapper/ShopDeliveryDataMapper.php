<?php

namespace App\Form\DataMapper;

use App\Entity\ShopDelivery;
use Symfony\Component\Form\FormInterface;
use Traversable;

class ShopDeliveryDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param ShopDelivery $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        $parameterTitle = $parameters['title'];
        if ($parameterTitle) {
            if ($viewData->getId() === null) {
                $viewData = new ShopDelivery($parameterTitle, $parameters['priceNet'],
                    $parameters['priceGross'], $parameters['freeDelivery']);
            } else {
                $viewData->setName($parameterTitle);
                $viewData->setPriceNet($parameters['priceNet']);
                $viewData->setPriceGross($parameters['priceGross']);
                $viewData->setFreeDelivery($parameters['freeDelivery']);
            }
        }
    }
}
