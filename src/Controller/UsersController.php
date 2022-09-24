<?php

namespace App\Controller;

use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('users')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'users.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $users = $repository->findAll();
        return $this->render('users/index.html.twig', ['users' => $users]);
    }

    #[Route('/all/{page?1}/{nbre?12}', name: 'users.list.all')]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $nbUsers = $repository->count([]);
        $nbPages = ceil($nbUsers / $nbre);
        
        $users = $repository->findBy([], ['age' => 'ASC'], $nbre, offset: ($page - 1) * $nbre);
        return $this->render('users/index.html.twig', [
            'users' => $users,
            'isPaginated' => true,
            'nbPages' => $nbPages,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'users.detail')]
    public function detail(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $user = $repository->find($id);
        if(!$user){
            $this->addFlash('error', "La personne d'id $id n'existe pas");
            return $this->redirectToRoute('users.list');
        }
        return $this->render('users/detail.html.twig', ['user' => $user]);
    }
    
    #[Route('/add', name: 'users.add')]
    public function addUser(ManagerRegistry $doctrine): Response
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

    #[Route('/delete', name: 'users.delete')]
    public function deleteUser(): Response
    {
        
    }
}
