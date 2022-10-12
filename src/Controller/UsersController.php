<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\User;
use App\Events\AddUserEvent;
use App\Events\ListAllUserEvent;
use App\Form\UsersType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

#[
    Route('users'),
    IsGranted('ROLE_USER')
]
class UsersController extends AbstractController
{
    
    public function __construct(
        private LoggerInterface $logger, 
        private Helpers $helper,
        private EventDispatcherInterface $dispatcher
        ) {}

    // Ci-dessus revient à faire : 
    // private $logger;
    // public function __construct(LoggerInterface $logger)
    // {
    //     $this->logger = $logger;
    // }
    // disponible à partir de la v8 de PHP

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

    #[Route('/pdf/{id}', name: 'user.pdf')]
    public function generatePdfUser(Users $user = null, PdfService $pdf)
    {
        $html = $this->render('users/detail.html.twig', ['user' => $user]);
        $pdf->showPdfFile($html);
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

    #[
        Route('/all/{page?1}/{nbre?12}', name: 'users.list.all'),
        IsGranted("ROLE_USER")    
    ]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        echo($this->helper->sayCc());
        
        $repository = $doctrine->getRepository(Users::class);
        $nbUsers = $repository->count([]);
        $nbPages = ceil($nbUsers / $nbre);
        
        $users = $repository->findBy([], ['age' => 'ASC'], $nbre, offset: ($page - 1) * $nbre);

        $listAllUserEvent = new ListAllUserEvent($nbUsers);
        $this->dispatcher->dispatch($listAllUserEvent, ListAllUserEvent::LIST_ALL_USERS_EVENT);

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
    
    #[Route('/edit/{id?=0}', name: 'users.edit')]
    public function addUser(
        Users $user = null, 
        ManagerRegistry $doctrine, 
        Request $request,
        UploaderService $uploaderService,
        // MailerService $mailerService
        ): Response
    {
        $this->denyAccessUnlessGranted(attribute:'ROLE_ADMIN');
        $new = false;
        if(!$user)        
        {
            $new = true;
            $user = new Users;
        }
        $form = $this->createForm(UsersType::class, $user);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->remove('role');
        //Mon formulaire va traiter la requête (ci-dessous)
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'image' field is not required
            // so the image must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('user_image_directory');

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImage($uploaderService->uploadFile($photo, $directory));
            }
            
            if($new)
            {
                $message = " a été créé avec succès";
                $user->setCreatedBy($this->getUser());
            }else{
                $message = " a été mis à jour avec succès";
            }
            
            $entityManager = $doctrine->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            if($new)
            {
                $addUserEvent = new AddUserEvent($user);
                $this->dispatcher->dispatch($addUserEvent, AddUserEvent::ADD_USER_EVENT);
            }

            $this->addFlash('success', $user->getUsername(). $message);

            return $this->redirectToRoute('users.list.all');
        }else{
            return $this->render('users/add-user.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[
        Route('/delete/{id}', name: 'users.delete'),
        IsGranted('ROLE_ADMIN')
    ]
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
