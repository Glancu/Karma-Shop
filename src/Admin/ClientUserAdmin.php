<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class ClientUserAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('firstName', null, ['label' => 'First name'])
                   ->add('lastName', null, ['label' => 'Last name'])
                   ->add('email', EmailType::class, ['label' => 'E-mail'])
                   ->add('password', RepeatedType::class, [
                       'type' => PasswordType::class,
                       'first_options' => ['label' => 'Password'],
                       'second_options' => ['label' => 'Password confirmation']
                   ])
                   ->add('phoneNumber', null, ['label' => 'Phone number'])
                   ->add('postalCode', null, ['label' => 'Postal code'])
                   ->add('city', null, ['label' => 'City'])
                   ->add('country', null, ['label' => 'Country'])
                   ->add('street', null, ['label' => 'Street']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email', null, ['label' => 'E-mail'])
                       ->add('street', null, ['label' => 'Street']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('firstName', null, ['label' => 'First name'])
                   ->add('lastName', null, ['label' => 'Last name'])
                   ->add('email', null, ['label' => 'E-mail'])
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
                   ->add('uuid', null, ['label' => 'UUID'])
                   ->add('firstName', null, ['label' => 'First name'])
                   ->add('lastName', null, ['label' => 'Last name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('phoneNumber', null, ['label' => 'Phone number'])
                   ->add('postalCode', null, ['label' => 'Postal code'])
                   ->add('city', null, ['label' => 'City'])
                   ->add('country', null, ['label' => 'Country'])
                   ->add('street', null, ['label' => 'Street']);
    }
}
