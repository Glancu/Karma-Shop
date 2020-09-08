<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class ProductReviewAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name', null, ['label' => 'Name'])
                       ->add('email', null, ['label' => 'E-mail']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('rating', null, ['label' => 'Rating'])
                   ->add('enable', null, ['label' => 'Enable', 'editable' => true])
                   ->add('createdAt', null, ['label' => 'Created'])
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
                   ->add('name', null, ['label' => 'Name'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('rating', null, ['label' => 'Rating'])
                   ->add('enable', null, ['label' => 'Enable'])
                   ->add('phoneNumber', null, ['label' => 'Phone number'])
                   ->add('createdAt', null, ['label' => 'Created'])
                   ->add('message', null, ['label' => 'Message']);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
    }
}
