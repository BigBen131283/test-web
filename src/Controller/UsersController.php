<?php

namespace App\Controller;

use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $this->render('users/index.html.twig', [
            'users' => $users,
            'isPaginated' => false
        ]);
    }

    #[Route('/all/age/{min?18}/{max?99}', name: 'users.list.age')]
    public function usersByAge(ManagerRegistry $doctrine, $min, $max): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $users = $repository->findUsersByAgeInterval($min, $max);
        return $this->render('users/index.html.twig', [
            'users' => $users,
            'isPaginated' => false
        ]);
    }

    #[Route('/stats/age/{min?18}/{max?99}', name: 'users.list.stats')]
    public function statsUsersByAge(ManagerRegistry $doctrine, $min, $max): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $stats = $repository->statUsersByAgeInterval($min, $max);
        return $this->render('users/stats.html.twig', [
            'stats' => $stats[0],
            'min' => $min,
            'max' => $max
        ]);
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

        // $timestamp = new DateTimeImmutable();

        $user = new Users();
        $user->setUsername('Ben');
        $user->setEmail('tono@saucisson.fr');
        $user->setPassword('123456');
        $user->setRole('10');
        $user->setAge('25');

        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->render('users/detail.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'users.delete')]
    public function deleteUser(Users $user = null, ManagerRegistry $doctrine): RedirectResponse //param converter
    {
        if($user){
            $manager = $doctrine->getManager();
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', "L'utilisateur a été supprimé avec succès");
        } else{
            $this->addFlash('error', 'Utilisateur inexistant ou déjà supprimé');
        }
        return $this->redirectToRoute('users.list.all');
    }

    #[Route('/update/{id}/{username}/{age}', name: 'users.update')]
    public function updateUser(Users $user = null, ManagerRegistry $doctrine, $username, $age): RedirectResponse
    {
        if($user){
            $user->setUsername($username);
            $user->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "L'utilisateur a été mis à jour avec succès");
        } else{
            $this->addFlash('error', 'Utilisateur inexistant');
        }
        return $this->redirectToRoute('users.list.all');
    }
}
