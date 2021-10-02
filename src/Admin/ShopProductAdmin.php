<?php

namespace App\Admin;

use App\Form\DataMapper\ShopProductDataMapper;
use App\Form\Type\Admin\ShopProductSpecificationTypeType;
use App\Service\MoneyService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ShopProductAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->with('General')
                   ->add('name', TextType::class, ['label' => 'Name'])
                   ->add('priceNet', MoneyType::class, [
                       'label' => 'Price net',
                       'scale' => 2,
                       'divisor' => MoneyService::PRICE_DIVIDE_MULTIPLY
                   ])
                   ->add('priceGross', MoneyType::class, [
                       'label' => 'Price gross',
                       'scale' => 2,
                       'divisor' => MoneyService::PRICE_DIVIDE_MULTIPLY
                   ])
                   ->add('enable', CheckboxType::class, [
                       'label' => 'Enable',
                       'required' => false
                   ])
                   ->add('quantity', IntegerType::class, ['label' => 'Quantity'])
                   ->add('shopBrand', null, ['label' => 'Brand'])
                   ->add('shopCategory', null, ['label' => 'Category'])
                   ->add('shopColors', null, ['label' => 'Colors'])
                   ->end()
                   ->with('Images')
                   ->add('images', ModelType::class, [
                       'label' => 'Images',
                       'multiple' => true,
                   ])
                   ->end()
                   ->with('Product specifications')
                   ->add('shopProductSpecifications', CollectionType::class, [
                       'label' => 'Product specifications',
                       'allow_add' => true,
                       'allow_delete' => true,
                       'entry_type' => ShopProductSpecificationTypeType::class
                   ], ['edit' => 'inline', 'inline' => 'table'])
                   ->end();

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new ShopProductDataMapper());
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name', null, ['label' => 'Name']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('shopBrand', null, ['label' => 'Brand'])
                   ->add('priceNet', MoneyType::class, [
                       'label' => 'Price net',
                       'template' => 'admin/list_price_int.html.twig'
                   ])
                   ->add('priceGross', MoneyType::class, [
                       'label' => 'Price gross',
                       'template' => 'admin/list_price_int.html.twig'
                   ])
                   ->add('quantity', IntegerType::class, ['label' => 'Quantity'])
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
        $showMapper->with('General')
                   ->add('id', null, ['label' => 'ID'])
                   ->add('uuid', null, ['label' => 'UUID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('slug', null, ['label' => 'Slug'])
                   ->add('createdAt', null, ['label' => 'Created At'])
                   ->add('priceNet', null, [
                       'label' => 'Price net',
                       'template' => 'admin/show_price_int.html.twig'
                   ])
                   ->add('priceGross', null, [
                       'label' => 'Price gross',
                       'template' => 'admin/show_price_int.html.twig'
                   ])
                   ->add('quantity', null, ['label' => 'Quantity'])
                   ->add('shopBrand', null, ['label' => 'Brand'])
                   ->add('shopCategory', null, ['label' => 'Category'])
                   ->add('shopColors', null, ['label' => 'Colors'])
                   ->add('description', 'html', ['label' => 'Description'])
                   ->end()
                   ->with('Images')
                   ->add('images', null, [
                       'label' => 'Images',
                       'template' => 'admin/show_sonata_media_images.html.twig'
                   ])
                   ->end()
                   ->with('Product specifications')
                   ->add('shopProductSpecifications', null, [
                       'label' => 'Product specifications',
                       'template' => 'admin/show_product_products_specifications.html.twig'
                   ])
                   ->end()
                   ->with('Reviews')
                   ->add('reviews', null, ['label' => 'Reviews', 'route' => ['name' => 'show']])
                   ->end();
    }
}
