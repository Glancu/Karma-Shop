<?php

namespace App\Admin;

use App\Form\DataMapper\ShopBrandDataMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ShopBrandAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('title', TextType::class, ['label' => 'Title'])
                   ->add('enable', CheckboxType::class, ['label' => 'Enable', 'required' => false]);

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new ShopBrandDataMapper());
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('title', null, ['label' => 'Title']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('title', null, ['label' => 'Title'])
                   ->add('enable', null, ['label' => 'Enable', 'editable' => true])
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
        $showMapper->add('title', null, ['label' => 'Title'])
                   ->add('slug', null, ['label' => 'Slug'])
                   ->add('enable', null, ['label' => 'Enable'])
                   ->add('createdAt', null, ['label' => 'Created']);
    }
}
