<?php

namespace App\EventListener;

use App\Events\AddUserEvent;
use App\Events\ListAllUserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class UsersListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function onAddUserListener(AddUserEvent $event)
    {
        $this->logger->debug("Hello, I'm listening the user.add et une personne vient d'être ajoutée");
    }

    public function onListAllUsers(ListAllUserEvent $event)
    {
        $this->logger->debug("Hello, I'm listening the users.list.all");
    }

    public function logKernelRequest(KernelEvent $event)
    {
        dd($event->getRequest());
    }
}