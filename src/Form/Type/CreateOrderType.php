<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\CreateOrderFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('personalData', CollectionType::class, ['allow_add' => true])
                ->add('methodPayment')
                ->add('isCustomCorrespondence')
                ->add('products')
                ->add('dataProcessingAgreement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateOrderFormModel::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

    public function getName(): string
    {
        return 'order';
    }
}
