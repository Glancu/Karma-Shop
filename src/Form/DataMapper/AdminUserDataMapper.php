<?php

namespace App\Form\DataMapper;

use App\Entity\AdminUser;
use Symfony\Component\Form\FormInterface;
use Traversable;

class AdminUserDataMapper extends BaseDataMapper implements DataMapperInterface
{
    /**
     * @param FormInterface[]|Traversable $forms
     * @param AdminUser $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        if ($viewData->getId() === null) {
            $viewData = new AdminUser($parameters['email'], $parameters['username'],
                $parameters['password'], ['ROLE_ADMIN']);
        } elseif($parameters['password']) {
            $viewData->setPassword($parameters['password']);
        }
    }
}
