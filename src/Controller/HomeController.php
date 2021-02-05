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
    /**
     * @Route("/home", name="home")
     */
    public function authentif(Request $request)
    {
        $participant = $this->getUser();

        return $this->render('main/home.html.twig',

        );
    }
}