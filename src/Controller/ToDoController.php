<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class ToDoController extends AbstractController
{
    #[Route('/', name: 'app_to_do')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        // afficher le tableau de todo
        if(!$session->has('todo')) {
            $todo = array(
                'achat' => 'Acheter une clé usb',
                'cours' => 'Finaliser mon cours',
                'correction' => 'Corriger mes examens'
            );
            $session->set('todo', $todo);
            $this->addFlash(type: 'info', 
                            message:"La ToDo List vient d'être créée");
        }
        
        return $this->render('to_do/index.html.twig');
    }

    #[Route('/add/{name}/{content}', name: 'todo.add',
                                    defaults: ['content' => 'piscine',
                                                'name'=>'vendredi'])]
    public function addToDo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        if($session->has(name: 'todo')) {
            $todo = $session->get('todo');
            if(isset($todo[$name])){
                $this->addFlash(type: 'error', 
                                message:"La ToDo List contient déjà la tâche $name");
            }
            else{
                $todo[$name] = $content;
                $session->set('todo', $todo);
                $this->addFlash(type: 'success', 
                                message:"La tâche $name a été ajoutée à la ToDo list");
            }
        }
        else {
            $this->addFlash(type: 'error', 
                            message:"La ToDo List n'est pas encore créée");
        }
        return $this->redirectToRoute(route: 'app_to_do');
    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateToDo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        if($session->has(name: 'todo')) {
            $todo = $session->get('todo');
            if(!isset($todo[$name])){
                $this->addFlash(type: 'error', 
                                message:"La ToDo List ne contient pas la tâche $name");
            }
            else{
                $todo[$name] = $content;
                $session->set('todo', $todo);
                $this->addFlash(type: 'success', 
                                message:"La tâche $name a été modifiée");
            }
        }
        else {
            $this->addFlash(type: 'error', 
                            message:"La ToDo List n'est pas encore créée");
        }
        return $this->redirectToRoute(route: 'app_to_do');
    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteToDo(Request $request, $name): RedirectResponse
    {
        $session = $request->getSession();
        if($session->has(name: 'todo')) {
            $todo = $session->get('todo');
            if(!isset($todo[$name])){
                $this->addFlash(type: 'error', 
                                message:"La ToDo List ne contient pas la tâche $name");
            }
            else{
                unset($todo[$name]);
                $session->set('todo', $todo);
                $this->addFlash(type: 'success', 
                                message:"La tâche $name a été supprimée");         
            }
        }
        else {
            $this->addFlash(type: 'error', 
                            message:"La ToDo List n'est pas encore créée");
        }
        return $this->redirectToRoute(route: 'app_to_do');
    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetToDo(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove(name: 'todo');
        $this->addFlash(type: 'success', 
                        message:"La ToDo List a été réinitialisée");
        
        return $this->redirectToRoute(route: 'app_to_do');
    }
}