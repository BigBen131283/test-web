<?php

namespace App\Controller;

use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/users/add', name: 'users.add')]
    public function addUser(PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $timestamp = new DateTimeImmutable();

        $user = new Users();
        $user->setUsername('Ben');
        $user->setEmail('tono@saucisson.fr');
        $user->setPassword('123456');
        $user->setRole('10');
        $user->setAge('25');
        $user->setCreatedAt($timestamp);

        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->render('users/detail.html.twig', [
            'user' => $user,
        ]);
    }
}
