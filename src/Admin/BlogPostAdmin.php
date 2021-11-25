<?php

namespace App\Admin;

use App\Form\DataMapper\BlogPostDataMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class BlogPostAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title', null, ['label' => 'Title'])
            ->add('shortContent', CKEditorType::class, ['label' => 'Short content'])
            ->add('longContent', CKEditorType::class, ['label' => 'Long content'])
            ->add('category', null, ['label' => 'Category'])
            ->add('image', ModelListType::class, ['label' => 'Post image'])
            ->add('tags', null, ['label' => 'Tags'])
            ->add('author', HiddenType::class, [
                'label' => 'Author',
                'data' => $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser()
            ]);

        $entityManager = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new BlogPostDataMapper($entityManager));
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('title', null, ['label' => 'Title'])
                   ->add('category', null, ['label' => 'Category'])
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
        $showMapper->add('id', null, ['label' => 'ID'])
                   ->add('uuid', null, ['label' => 'Uuid'])
                   ->add('title', null, ['label' => 'Title'])
                   ->add('slug', null, ['label' => 'Slug'])
                   ->add('shortContent', 'html', ['label' => 'Short content'])
                   ->add('longContent', 'html', ['label' => 'Long content'])
                   ->add('category', null, ['label' => 'Category'])
                   ->add('image', null, ['label' => 'Post image'])
                   ->add('tags', null, ['label' => 'Tags'])
                   ->add('createdAt', null, ['label' => 'Created at']);
    }
}
