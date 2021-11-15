<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\EmailHistory;
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
