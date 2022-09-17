<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(Request $request): Response
    {
        $session = $request->getSession(); // = session_start() en php
        if($session->has(name: 'nbvisit')) {
            $nbvisit = $session->get(name: 'nbvisit') + 1;
        }
        else {
            $nbvisit = 1;
        }
        $session->set('nbvisit', $nbvisit);
        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
        ]);
    }
}
