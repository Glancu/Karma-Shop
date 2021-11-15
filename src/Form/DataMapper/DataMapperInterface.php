<?php

namespace App\Form\DataMapper;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Traversable;

interface DataMapperInterface
{
    /**
     * Maps the model data of a list of children forms into the view data of their parent.
     *
     * This is the internal cascade call of FormInterface::submit for compound forms, since they
     * cannot be bound to any input nor the request as scalar, but their children may:
     *
     *     $compoundForm->submit($arrayOfChildrenViewData)
     *     // inside:
     *     $childForm->submit($childViewData);
     *     // for each entry, do the same and/or reverse transform
     *     $this->dataMapper->mapFormsToData($compoundForm, $compoundInitialViewData)
     *     // then reverse transform
     *
     * When a simple form is submitted the following is happening:
     *
     *     $simpleForm->submit($submittedViewData)
     *     // inside:
     *     $this->viewData = $submittedViewData
     *     // then reverse transform
     *
     * The model data can be an array or an object, so this second argument is always passed
     * by reference.
     *
     * @param FormInterface[]|Traversable $forms    A list of {@link FormInterface} instances
     * @param mixed                        $viewData The compound form's view data that get mapped
     *                                               its children model data
     *
     * @throws UnexpectedTypeException if the type of the data parameter is not supported
     */
    public function mapFormsToData($forms, &$viewData): void;
}
