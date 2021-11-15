<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\CreateCommentFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name')
                ->add('email', EmailType::class)
                ->add('message')
                ->add('productUuid')
                ->add('dataProcessingAgreement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommentFormModel::class,
            'csrf_protection' => false
        ]);
    }

    public function getName(): string
    {
        return 'create_comment';
    }
}
