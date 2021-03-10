<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ShopDeliveryAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name', TextType::class, ['label' => 'Name'])
                   ->add('freeDelivery', CheckboxType::class, [
                       'label' => 'Free delivery',
                       'required' => false
                   ])
                   ->add('priceNet', MoneyType::class, ['label' => 'Price net', 'required' => false])
                   ->add('priceGross', MoneyType::class, ['label' => 'Price gross', 'required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name', null, ['label' => 'Name']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('freeDelivery', null, ['label' => 'Free delivery', 'editable' => true])
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
                   ->add('freeDelivery', null, ['label' => 'Free delivery'])
                   ->add('priceNet', null, ['label' => 'Price net'])
                   ->add('priceGross', null, ['label' => 'Price gross']);
    }
}
