<?php

namespace App\Admin;

use App\Entity\EmailTemplate;
use App\Form\DataMapper\EmailTemplateDataMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class EmailTemplateAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name', TextType::class, ['label' => 'Name'])
                   ->add('type', ChoiceType::class, [
                       'choices' => array_flip(EmailTemplate::getTypesArr())
                   ])
                   ->add('subject', TextType::class, ['label' => 'Subject'])
                   ->add('message', CKEditorType::class, [
                       'label' => 'Message',
                       'help' => '
                            Available variables to use in message: <br>
                            <b>%cart%</b> - add cart with products and total price <br>
                            <b>%order_number%</b> - order number <br>
                            <b>%client_email%</b> - client email <br>
                            <b>%method_payment%</b> - method payment
                       '
                   ]);

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new EmailTemplateDataMapper());
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('type', null, ['label' => 'Type'])
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
        $showMapper->add('name', null, ['label' => 'Name'])
                   ->add('type', null, ['label' => 'Type'])
                   ->add('subject', null, ['label' => 'Subject'])
                   ->add('message', null, ['label' => 'Message']);
    }
}
