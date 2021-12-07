<?php

namespace App\Admin;

use Beelab\PaypalBundle\Entity\Transaction;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PayPalTransactionAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('status', 'doctrine_orm_choice', ['label' => 'Status'], ChoiceType::class, [
                'choices' => array_flip(Transaction::$statuses),
                'multiple' => false
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('order', null, ['label' => 'Order'])
                   ->add('statusLabel', null, ['label' => 'Status'])
                   ->add('start', null, ['label' => 'Start at'])
                   ->add('end', null, ['label' => 'End at'])
                   ->add('amount', null, ['label' => 'Amount'])
                   ->add('_action', null, [
                       'actions' => [
                           'show' => [],
                           'edit' => [],
                           'delete' => []
                       ]
                   ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper->add('id', null, ['label' => 'ID'])
                   ->add('order', null, ['label' => 'Order', 'route' => ['name' => 'show']])
                   ->add('statusLabel', null, ['label' => 'Status'])
                   ->add('start', null, ['label' => 'Start at'])
                   ->add('end', null, ['label' => 'End at'])
                   ->add('amount', null, ['label' => 'Amount']);
    }
}
