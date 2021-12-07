<?php

namespace App\Admin;

use App\Component\OrderStatus;
use App\Entity\Order;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class OrderAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => array_flip(OrderStatus::getStatusesArr())
            ])
            ->add('methodPayment', ChoiceType::class, [
                'label' => 'Method pay',
                'choices' => array_flip(Order::getMethodPaymentsArr())
            ])
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
                   ->add('priceNetFloat', null, ['label' => 'Price net'])
                   ->add('priceGrossFloat', null, ['label' => 'Price gross'])
                   ->add('statusStr', null, ['label' => 'Status'])
                   ->add('transaction', null, ['label' => "PayPal", 'route' => ['name' => 'show']])
                   ->add('cart', null, [
                       'label' => 'Cart',
                       'mapped' => false,
                       'template' => 'admin/order/_show_cart.html.twig'
                   ]);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('updateStatus', $this->getRouterIdParameter() . '/update-status');
    }
}
