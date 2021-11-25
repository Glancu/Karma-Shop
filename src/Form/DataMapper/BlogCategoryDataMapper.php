<?php

namespace App\Form\DataMapper;

use App\Entity\BlogCategory;
use Symfony\Component\Form\FormInterface;
use Traversable;

class BlogCategoryDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param BlogCategory() $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new BlogCategory($parameters['name']);
        } else {
            $viewData->setName($parameters['name']);
        }
    }
}
