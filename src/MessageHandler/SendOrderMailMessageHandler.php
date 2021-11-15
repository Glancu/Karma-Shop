<?php

namespace App\MessageHandler;

use App\Message\SendOrderMailMessage;
use App\Service\MailerService;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendOrderMailMessageHandler implements MessageHandlerInterface
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SendOrderMailMessage $mail): void
    {
        $this->mailerService->sendMail(
            $mail->getEmailSubject(),
            $mail->getEmailContent(),
            $mail->getEmailTo()
        );
    }
}
