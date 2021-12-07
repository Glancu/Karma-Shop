<?php

namespace App\MessageHandler;

use App\Message\SendMailMessage;
use App\Service\MailerService;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendMailMessageHandler implements MessageHandlerInterface
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(SendMailMessage $mail): void
    {
        $this->mailerService->sendMail(
            $mail->getEmailSubject(),
            $mail->getEmailContent(),
            $mail->getEmailTo()
        );
    }
}
