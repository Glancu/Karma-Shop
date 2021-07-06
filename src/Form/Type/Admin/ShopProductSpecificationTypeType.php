<?php
declare(strict_types=1);

namespace App\Form\Type\Admin;

use App\Entity\ShopProductSpecification;
use App\Entity\ShopProductSpecificationType as ProductSpecificationTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopProductSpecificationTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('shopProductSpecificationType', EntityType::class, [
                    'label' => 'Product specification type',
                    'class' => ProductSpecificationTypeEntity::class
                ])
                ->add('value', TextType::class, ['label' => 'Value']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => ShopProductSpecification::class,
            'empty_data' => function(FormInterface $form) {
                return new ShopProductSpecification($form->get('shopProductSpecificationType')->getData(), $form->get('value')->getData());
            }
        ));
    }
}
