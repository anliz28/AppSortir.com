<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    //méthode qui affiche la page home avec l'utilisateur en cours
    /**
     * @Route("/home", name="home")
     */
    public function authentif(Request $request)
    {
        $participant = $this->getUser();

        return $this->render('main/home.html.twig');
    }
}