<?php

namespace App\Form\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CreateOrderProductType
 *
 * @package App\Form\Component
 */
class CreateOrderProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('uuid', TextType::class, ['required' => true])
                ->add('quantity', IntegerType::class, ['required' => true]);
    }
}
