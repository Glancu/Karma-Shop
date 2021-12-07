<?php

namespace App\DataFixtures;

use App\Entity\EmailTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmailTemplateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sendMailToAdminWhenUserOrderAndOrder = new EmailTemplate(
            'Send mail to admin when user order an order',
            'User %client_email% order an order %order_uuid%',
            '
                <p>User <strong>%client_email%</strong> order an order <strong>%order_uuid%.</strong></p>

                <p>Method payment: <strong>%method_payment%</strong></p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_NEW_ORDER_TO_ADMIN
        );
        $manager->persist($sendMailToAdminWhenUserOrderAndOrder);

        $sendMailToUserWhenUserOrderAnOrder = new EmailTemplate(
            'Send mail to user when user order an order',
            'Thanks for your order %order_uuid%!',
            '
                <p>Hello,</p>
    
                <p>thanks for your order <strong>%order_uuid%!</strong></p>
                
                <p>Method payment: <strong>%method_payment%</strong></p>
                
                <p>%pay_pal_block_start%</p>
                
                <p>PayPalUrl: <a href="%pay_pal_url%?notifyUrl=http://local.karma.pl/payment/pay-pal/notify">%pay_pal_url%?notifyUrl=http://local.karma.pl/payment/pay-pal/notify</a></p>
                
                <p>%pay_pal_block_end%</p>
                
                <p>%payment_online_start%<br />
                Here put your data to payment online<br />
                %payment_online_end%</p>
                
                <p>Cart:</p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_NEW_ORDER_TO_USER
        );
        $manager->persist($sendMailToUserWhenUserOrderAnOrder);

        $sendMailToAdminWhenUserContactFormFromContactForm = new EmailTemplate(
            'Send mail to admin when someone send message by contact form',
            'New message from contact form!',
            'Someone send message from contact form. Go to admin panel and check details!',
            EmailTemplate::TYPE_NEW_CONTACT_TO_ADMIN
        );
        $manager->persist($sendMailToAdminWhenUserContactFormFromContactForm);

        $sendMailToUserWhenOrderWasNotPaid = new EmailTemplate(
            'Send mail to user when order was not paid',
            'We remind you to pay for the order %order_uuid%',
            '
                <p>Hello %client_email%,</p>

                <p>I remind you to pay for the order %order_uuid%.</p>
                
                <p>Method payment: <strong>%method_payment%</strong></p>
            
                <p>%pay_pal_block_start%</p>
                
                <p>PayPalUrl: <a href="%pay_pal_url%?notifyUrl=http://local.karma.pl/payment/pay-pal/notify">%pay_pal_url%?notifyUrl=http://local.karma.pl/payment/pay-pal/notify</a></p>
                
                <p>%pay_pal_block_end%</p>
                
                <p>Your products order:</p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_ORDER_NOT_PAID_TO_USER
        );
        $manager->persist($sendMailToUserWhenOrderWasNotPaid);

        $sendMailToUserWhenOrderWasPaid = new EmailTemplate(
            'Send mail to user when order was paid',
            'Your order was paid!',
            '
                <p>Hello <strong>%client_email%</strong>,</p>

                <p>your order <strong>%order_uuid%</strong> was paid!</p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_ORDER_PAID_TO_USER
        );
        $manager->persist($sendMailToUserWhenOrderWasPaid);

        $sendMailToUserWhenOrderProductsWasSent = new EmailTemplate(
            'Send mail to user when order products was sent',
            'Your products was sent to you!',
            '
                <p>Hello <strong>%client_email%</strong>,</p>

                <p>Your products from order %order_uid% was sent to you!</p>
                
                <p>Wait for a courier!</p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_ORDER_SENT_PRODUCTS_TO_USER
        );
        $manager->persist($sendMailToUserWhenOrderProductsWasSent);

        $sendMailToUserWhenOrderStatusIsInProgress = new EmailTemplate(
            'Send mail to user when order status is in progress',
            'We are working on your order',
            '
                <p>Hello <strong>%client_email%</strong>,</p>

                <p>your order <strong>%order_number%</strong> is now in progress!</p>
                
                <p>We will send u a mail when we sent products to you!</p>
            ',
            EmailTemplate::TYPE_ORDER_IN_PROGRESS_TO_USER
        );
        $manager->persist($sendMailToUserWhenOrderStatusIsInProgress);

        $sendMailToAdminWhenOrderWasPaid = new EmailTemplate(
            'Send mail to admin when order was paid',
            'Order %order_uuid% has now been paid',
            '
                <p>Order %order_uuid% has been paid now!<br /></p>
                
                <p>%cart%</p>
            ',
            EmailTemplate::TYPE_ORDER_PAID_TO_ADMIN
        );
        $manager->persist($sendMailToAdminWhenOrderWasPaid);

        $manager->flush();

        $sendMailToUserWithNewPassword = new EmailTemplate(
            'Send mail to user with new password',
            'Your new password!',
            '
                <p>Hello %client_email%,</p>

                <p>your new password is: <strong>%client_new_password%</strong></p>
            ',
            EmailTemplate::TYPE_USER_FORGOT_PASSWORD
        );
        $manager->persist($sendMailToUserWithNewPassword);
    }
}
