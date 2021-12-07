<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\EmailHistory;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_TransportException;

final class MailerService
{
    private Swift_Mailer $swiftMailer;
    private EntityManagerInterface $entityManager;
    private string $from;
    private string $sender;
    private string $replyTo;

    public function __construct(
        Swift_Mailer $swiftMailer,
        EntityManagerInterface $entityManager,
        string $from,
        string $sender,
        string $replyTo
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->entityManager = $entityManager;
        $this->from = $from;
        $this->sender = $sender;
        $this->replyTo = $replyTo;
    }

    /**
     * @throws Exception
     */
    public function sendMail(
        string $subject,
        string $body,
        string $emailTo,
        ?string $reply = null
    ): void {
        $swiftMailer = $this->swiftMailer;

        $msg = $this->createMessage($subject, $body, $emailTo, $reply);

        if (!$swiftMailer->gettransport()->isstarted()) {
            $swiftMailer->gettransport()->start();
        }

        try {
            $swiftMailer->send($msg);

            $this->createEmailHistory($subject, $body, $emailTo);
        } catch (Swift_TransportException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getAdminEmail(): string
    {
        return $this->sender;
    }

    public function replaceVariablesOrderForEmail(Order $order, string $text, array $options = []): string
    {
        $text = str_replace(
            [
                '%client_email%',
                '%method_payment%',
                '%order_uuid%',
                '%pay_pal_url%'
            ],
            [
                $order->getUser()->getEmail(),
                $order->getMethodPaymentStr(),
                $order->getUuid(),
                $order->isMethodPayPal() && isset($options['payPalUrl']) ? $options['payPalUrl'] : '',
            ],
            $text
        );

        preg_match('~%pay_pal_block_start%([^{]*)%pay_pal_block_end%~i', $text, $textOnlyForPayPal);

        if (isset($textOnlyForPayPal[0]) && !$order->isMethodPayPal()) {
            $text = str_replace($textOnlyForPayPal[0], '', $text);
        }

        preg_match('~%payment_online_start%([^{]*)%payment_online_end%~i', $text, $textOnlyForOnlinePayment);

        if (isset($textOnlyForOnlinePayment[0]) && $order->isMethodPayPal()) {
            $text = str_replace($textOnlyForOnlinePayment[0], '', $text);
        }

        return str_replace(
            [
                '%pay_pal_block_start%',
                '%pay_pal_block_end%',
                '%payment_online_start%',
                '%payment_online_end%'
            ],
            [
                '',
                '',
                '',
                ''
            ],
            $text
        );
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $emailTo
     * @param string|null $reply
     *
     * @return Swift_Message
     */
    private function createMessage(
        string $subject,
        string $body,
        string $emailTo,
        ?string $reply = null
    ): Swift_Message {
        return (new Swift_Message($subject))
            ->setFrom($this->from, $this->sender)
            ->setReplyTo($reply ? [$reply] : [$this->replyTo])
            ->setTo($emailTo)
            ->setBody(
                $body,
                'text/html'
            );
    }

    private function createEmailHistory(string $subject, string $body, string $emailTo): void
    {
        $emailHistory = new EmailHistory($emailTo, $subject, $body);

        $this->entityManager->persist($emailHistory);
        $this->entityManager->flush();
    }
}
