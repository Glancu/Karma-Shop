<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ClientUser;
use App\Form\Model\ClientUserChangePasswordFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientUserChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('oldPassword', PasswordType::class)
                ->add('newPassword', PasswordType::class)
                ->add('newPasswordRepeat', PasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientUserChangePasswordFormModel::class,
            'csrf_protection' => false
        ]);
    }

    public function getName(): string
    {
        return 'client_user_change_password';
    }
}
