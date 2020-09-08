<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ProductReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, ['label' => 'Name'])
                ->add('email', EmailType::class, ['label' => 'Email'])
                ->add('rating', IntegerType::class, ['label' => 'Rating', 'required' => true])
                ->add('phoneNumber', TelType::class, ['label' => 'Phone number'])
                ->add('message', TextType::class, ['label' => 'Message', 'required' => true])
                ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductReview::class,
            'csrf_protection' => false
        ]);
    }

    public function getName(): string
    {
        return 'product_review';
    }
}
