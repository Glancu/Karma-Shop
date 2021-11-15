<?php

namespace App\Message;

class SendOrderMailMessage
{
    private string $emailSubject;
    private string $emailContent;
    private string $emailTo;

    public function __construct(string $emailSubject, string $emailContent, string $emailTo)
    {
        $this->emailSubject = $emailSubject;
        $this->emailContent = $emailContent;
        $this->emailTo = $emailTo;
    }

    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    public function getEmailContent(): string
    {
        return $this->emailContent;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }
}
