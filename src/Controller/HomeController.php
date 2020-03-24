<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $str = 'Ma page d\'acceuil';
        return $this->render('home/index.html.twig', [
           'title' => $str,
        ]);
    }
}
