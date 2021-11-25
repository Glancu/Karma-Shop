<?php

namespace App\Form\DataMapper;

use App\Entity\BlogTag;
use Symfony\Component\Form\FormInterface;
use Traversable;

class BlogTagDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param BlogTag $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new BlogTag($parameters['name']);
        } else {
            $viewData->setName($parameters['name']);
        }
    }
}
