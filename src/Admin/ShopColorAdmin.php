<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Show\ShowMapper;

final class ShopColorAdmin extends AbstractAdmin {
    protected function configureFormFields(FormMapper $formMapper): void {
        $formMapper
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('enable', CheckboxType::class, ['label' => 'Enable', 'required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void {
        $datagridMapper
            ->add('name', null, ['label' => 'Name']);
    }

    protected function configureListFields(ListMapper $listMapper): void {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->add('name', null, ['label' => 'Name'])
            ->add('enable', null, ['label' => 'Enable', 'editable' => true])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void {
        $showMapper
            ->add('name', null, ['label' => 'Name'])
            ->add('slug', null, ['label' => 'Slug'])
            ->add('enable', null, ['label' => 'Enable'])
            ->add('createdAt', null, ['label' => 'Created']);
    }
}