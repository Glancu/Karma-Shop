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
                            <b>%client_email%</b> - client email (Available also in subject) <br>
                            <b>%method_payment%</b> - method payment (Available also in subject) <br>
                            <b>%pay_pal_block_start%</b> - Block start for information when payment method is PayPal <br>
                            <b>%pay_pal_block_end%</b> - Block end for information when payment method is PayPal <br>
                            <b>%pay_pal_url%</b> - Link to PayPal payment (Put between %pay_pal_block_start% and %pay_pal_block_end%) <b>(you nedd to add ?notifyUrl=http://yourwebsite.com/payment/pay-pal/notify , but change <small>http://yourwebsite.com</small> to your website url)</b><br>
                            <b>%order_uuid%</b> - Order number (Available also in subject)
                       '
                   ]);

        $builder = $formMapper->getFormBuilder();
        $builder->setDataMapper(new EmailTemplateDataMapper());
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('id', null, ['label' => 'ID'])
                   ->add('name', null, ['label' => 'Name'])
                   ->add('subject', null, ['label' => 'Subject'])
                   ->add('typeStr', null, ['label' => 'Type'])
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
