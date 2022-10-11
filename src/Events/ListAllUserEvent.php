<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ListAllUserEvent extends Event
{
    const LIST_ALL_USERS_EVENT = 'users.list.all';

    public function __construct(private int $nbUsers){}

    public function getUser(): int 
    {
        return $this->nbUsers;
    }
}