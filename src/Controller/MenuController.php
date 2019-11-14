<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;





class MenuController extends AbstractController
{
    //pas de route : c'est une vue partielle
    public function index()
    {
        $finder=new Finder();
        $finder->directories()->in("../public/photos");

        return $this->render('menu/_menu.html.twig', [
            "dossiers" => $finder,
        ]);
    }
}
