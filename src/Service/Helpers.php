<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class Helpers
{
    private $langue;
    public function __construct(private LoggerInterface $logger, Security $security) {}
    
    public function sayCc() {
        $this->logger->info(message: 'Je dis coucou');
    }

    public function getUser(): User
    {
        if($this->security->isGranted(attributes:'ROLE_ADMIN'))
        {
            $user = $this->security->getUser();
            if($user instanceof User)
            {
                return $user;
            }
        }
    }
}