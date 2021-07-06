<?php

namespace App\Form\DataMapper;

use Symfony\Component\Form\FormInterface;
use Traversable;

abstract class BaseDataMapper implements \Symfony\Component\Form\DataMapperInterface
{
    /**
     * @param object $viewData
     *
     * @param FormInterface[]|Traversable $forms
     */
    public function mapDataToForms($viewData, $forms): void
    {
        if ($viewData !== null) {
            $forms = iterator_to_array($forms);

            foreach (array_keys($forms) as $key) {
                $firstKeyUpper = ucfirst($key);

                $getter = "get${firstKeyUpper}";
                if($key === 'enable' || $key === 'freeDelivery') {
                    $getter = "is${firstKeyUpper}";
                }

                $forms[$key]->setData($viewData->$getter());
            }
        }
    }

    public static function getParametersFromForm($forms): array
    {
        $forms = iterator_to_array($forms);

        $parameters = [];

        foreach (array_keys($forms) as $key) {
            $parameters[$key] = $forms[$key]->getData();
        }

        return $parameters;
    }
}
