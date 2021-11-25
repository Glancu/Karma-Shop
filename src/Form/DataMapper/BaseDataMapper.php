<?php

namespace App\Form\DataMapper;

use Symfony\Component\Form\FormInterface;
use Traversable;
use Symfony\Component\Form\DataMapperInterface;

abstract class BaseDataMapper implements DataMapperInterface
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
                if($key === 'enable') {
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
