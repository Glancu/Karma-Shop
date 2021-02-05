<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ClientUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateClientUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstName', TextType::class)
                ->add('lastName', EmailType::class)
                ->add('email', EmailType::class)
                ->add('password', PasswordType::class)
                ->add('phoneNumber', TextType::class)
                ->add('postalCode', TextType::class)
                ->add('city', TextType::class)
                ->add('country', TextType::class)
                ->add('street', TextType::class)
                ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientUser::class,
            'csrf_protection' => false
        ]);
    }

    public function getName(): string
    {
        return 'client_user';
    }
}
