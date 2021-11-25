<?php

namespace App\Admin;

use App\Form\DataMapper\BlogTagDataMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class BlogTagAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name', null, ['label' => 'Name']);

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new BlogTagDataMapper());
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('countPosts', null, ['label' => 'Count posts'])
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
                   ->add('uuid', null, ['label' => 'Uuid'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('slug', null, ['label' => 'Slug'])
                   ->add('countPosts', null, ['label' => 'Count posts'])
                   ->add('createdAt', null, ['label' => 'Created at']);
    }
}
