<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/template', name: 'template')]
    public function template() 
    {
        return $this->render('template.html.twig');
    }

    #[Route('/order/{maVar}', name: 'test.order.route')]
    public function testOrderRoute($maVar): Response 
    {
        return new Response(
            content: "<html><body>$maVar</body></html>"
        );
    }
    
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('multi/{entier1}/{entier2}',
            name: 'multi',
            requirements: ['entier1' => '\d+','entier2' => '\d+'])]
    public function multiplication($entier1, $entier2): Response
    {
        $resultat = $entier1 * $entier2;
        return new Response(content: "<h1>$resultat</h1>");
    }
}
