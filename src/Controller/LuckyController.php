<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    public function number(): Response
    {
        $number = random_int(0, 100);
        $name = "Benjamin";
        $superArray = [
            'name'=>$name,
            'number'=>$number
        ];

        return $this->render('number.html.twig', [
            'number' => $number,
            'name' =>$name,
            'x'=> $superArray,
        ]);
    }
}