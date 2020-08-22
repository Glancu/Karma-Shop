<?php

namespace App\Admin;

use App\Entity\AdminUser;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class AdminUserAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('email', EmailType::class, ['label' => 'E-mail'])
                   ->add('username', TextType::class, ['label' => 'Username'])
                   ->add('password', RepeatedType::class, [
                       'type' => PasswordType::class,
                       'first_options' => ['label' => 'Password'],
                       'second_options' => ['label' => 'Password confirmation']
                   ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email', null, ['label' => 'E-mail'])->add('username', null, ['label' => 'Username']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('username', null, ['label' => 'Username'])
                   ->add('_action', null, [
                       'actions' => [
                           'show' => [],
                           'edit' => [],
                           'delete' => [],
                       ]
                   ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper->add('id', null, ['label' => 'ID'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('username', null, ['label' => 'Username']);
    }

    /**
     * @param AdminUser $adminUser
     */
    public function prePersist($adminUser): void {
        $plainPassword = $adminUser->getPassword();
        $container = $this->getConfigurationPool()->getContainer();
        if($container) {
            $encoder = $container->get('security.password_encoder');
            if($encoder) {
                $encoded = $encoder->encodePassword($adminUser, $plainPassword);

                $adminUser->setPassword($encoded);
                $adminUser->setRoles(['ROLE_ADMIN']);
            }
        }
    }
}
