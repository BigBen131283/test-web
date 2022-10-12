<?php

namespace App\EventSubscriber;

use App\Events\AddUserEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UsersEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerService $mailer) {}
    
    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        // retourne un tableau
        return [AddUserEvent::ADD_USER_EVENT => ['onAddUserEvent', 3000]
        ];
    }

    public function onAddUserEvent(AddUserEvent $event)
    {
        $user = $event->getUser();
        $mailMessage = $user->getUsername().' a été ajouté avec succès.';    
        $this->mailer->sendEmail(content: $mailMessage, subject: 'Mail sent from EventSubscriber');
    }
}