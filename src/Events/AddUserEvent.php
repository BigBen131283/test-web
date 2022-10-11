<?php

namespace App\Events;

use App\Entity\Users;
use Symfony\Contracts\EventDispatcher\Event;

class AddUserEvent extends Event
{
    const ADD_USER_EVENT = 'user.add';

    public function __construct(private Users $users){}

    public function getUser(): Users 
    {
        return $this->users;
    }
}