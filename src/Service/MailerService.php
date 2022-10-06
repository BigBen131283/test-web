<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $replyTo;
    public function __construct(private MailerInterface $mailer, $replyTo) 
    {
        $this->replyTo = $replyTo;
    }
    
    public function sendEmail(
        $from = 'exemple.noreply@gmail.com',
        $to = 'dummy-samble@email.com',
        $subject = 'Time for Symfony Mailer!',
        $content = '<p>See Twig integration for better HTML integration!</p>'
    ): Void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($this->replyTo)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);
    }
}