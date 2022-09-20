<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SampleController extends AbstractController
{
    #[Route('/sample/{name}/{firstname}', name: 'app_sample')]
    public function index(Request $request, $name, $firstname): Response
    {
        return $this->render('sample/index.html.twig', [
            'controller_name' => 'SampleController',
            'name' => $name,
            'firstname' => $firstname
        ]);
    }

    #[Route('/addition/{n1}/{n2}', name: 'addition')]
    public function sum($n1, $n2): Response
    {
        // $n1 = random_int(1,99);
        // $n2 = random_int(1,99);
        $sum = $n1 + $n2;
        return $this->render('sample/somme.html.twig', [
            'controller_name' => 'SampleController',
            'n1' => $n1,
            'n2' => $n2,
            'somme' => $sum
        ]);
    }

    // #[Route('/sayHello/{firstname}/{lastname}', name: 'say.hello')]
    public function sayHello(Request $request, $firstname, $lastname): Response
    {
        return $this->render('sample/hello.html.twig', [
            'prenom' => $firstname,
            'nom' => $lastname,
            // 'path' => 'moi.jpg'
        ]);
    }
}
