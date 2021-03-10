<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class CommentAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name', null, ['label' => 'Name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('enable', CheckboxType::class, ['label' => 'Enable', 'required' => false])
                   ->add('subject', null, ['label' => 'Subject', 'required' => false])
                   ->add('text', null, ['label' => 'Message']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email', null, ['label' => 'E-mail']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('enable', null, ['label' => 'Enable'])
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
                   ->add('name', null, ['label' => 'Name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('subject', null, ['label' => 'Subject'])
                   ->add('enable', null, ['label' => 'Enable'])
                   ->add('createdAt', null, ['label' => 'Created'])
                   ->add('products', null, ['label' => 'Products', 'route' => ['name' => 'show']])
                   ->add('text', null, ['label' => 'Message']);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
    }
}
