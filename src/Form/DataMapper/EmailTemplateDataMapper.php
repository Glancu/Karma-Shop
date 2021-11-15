<?php

namespace App\Form\DataMapper;

use App\Entity\EmailTemplate;
use Symfony\Component\Form\FormInterface;
use Traversable;

class EmailTemplateDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param EmailTemplate $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new EmailTemplate($parameters['name'], $parameters['subject'], $parameters['message'], $parameters['type']);
        } else {
            $viewData->setName($parameters['name']);
            $viewData->setSubject($parameters['subject']);
            $viewData->setMessage($parameters['message']);
            $viewData->setType($parameters['type']);
        }
    }
}
