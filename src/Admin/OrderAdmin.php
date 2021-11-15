<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

final class OrderAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('products', null, ['label' => 'Products'])
                   ->add('status', null, ['label' => 'Status'])
                   ->add('methodPay', null, ['label' => 'Method pay'])
                   ->add('additionalInformation', null, ['label' => 'Additional information']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('user', null, ['label' => 'User'])
                   ->add('priceNet', null, ['label' => 'Price net'])
                   ->add('priceGross', null, ['label' => 'Price gross'])
                   ->add('createdAt', null, ['label' => 'Created at'])
                   ->add('statusStr', null, ['label' => 'Status'])
                   ->add('_action', null, [
                       'actions' => [
                           'show' => [],
                           'edit' => [],
                           'delete' => [],
                           'updateStatus' => [
                               'template' => 'admin/order/CRUD/list__action_update_status.html.twig',
                           ],
                       ]
                   ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper->add('id', null, ['label' => 'ID'])
                   ->add('uuid', null, ['label' => 'UUID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('priceNet', null, ['label' => 'Price net'])
                   ->add('priceGross', null, ['label' => 'Price gross'])
                   ->add('statusStr', null, ['label' => 'Status']);

    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('updateStatus', $this->getRouterIdParameter() . '/update-status');
    }
}
