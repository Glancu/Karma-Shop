<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class ContactAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email', null, ['label' => 'E-mail']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('subject', null, ['label' => 'Subject'])
                   ->add('dataProcessingAgreement', null, [
                       'label' => 'Data Processing Agreement'
                   ])
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
                   ->add('createdAt', null, ['label' => 'Created'])
                   ->add('email', null, ['label' => 'E-mail'])
                   ->add('subject', null, ['label' => 'Subject'])
                   ->add('message', null, ['label' => 'Message'])
                   ->add('dataProcessingAgreement', null, [
                       'label' => 'Data Processing Agreement',
                       'template' => '@SonataAdmin/CRUD/show_boolean.html.twig'
                   ]);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
    }
}
