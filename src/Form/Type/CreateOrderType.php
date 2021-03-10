<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Newsletter;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('priceNet', MoneyType::class)
                ->add('priceGross', MoneyType::class)
                ->add('methodPay', null)
                ->add('additionalInformation', null, ['required' => false])
                ->add('products', null)
                ->add('delivery', null)
                ->add('user') // @TODO ? Nie wiem co tu będzie jeszcze dokładnie. Prawdopodobnie osobne fieldy na wszystko per user
                ->add('dataProcessingAgreement', CheckboxType::class);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'csrf_protection' => false
        ]);
    }

    public function getName(): string
    {
        return 'order';
    }
}
