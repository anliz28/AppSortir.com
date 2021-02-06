<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{


    /**
     * @Route("/connexion", name="login")
     * @param AuthenticationUtils $authentif
     * @return Response
     */
    public function login(AuthenticationUtils $authentif): Response
    {
        $error = $authentif->getLastAuthenticationError();
        $lastUserName = $authentif->getLastUsername();

        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->render('main/home.html.twig');
        }else{
            $this->addFlash('error', "L'identifiant ou le mot de passe est erroné");
             return $this->render('participants/login.html.twig',
            [
                'last_username' => $lastUserName,
                'error'=> $error
            ]
        );}
    }

    /**
     * @Route("/deconnexion", name="logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}