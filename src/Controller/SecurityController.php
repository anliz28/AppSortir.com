<?php


namespace App\Controller;



use App\Entity\Inscriptions;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
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

//méthode pour se connecter à son compte
    /**
     * @Route("/connexion", name="login")
     * @param AuthenticationUtils $authentif
     * @return Response
     */
    public function login(AuthenticationUtils $authentif, EntityManagerInterface $em, CampusRepository $campusRepository): Response
    {
        $error = $authentif->getLastAuthenticationError();
        $lastUserName = $authentif->getLastUsername();

        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $sortie = $em->getRepository(Sortie::class)->findAll();
            $inscriptions = $em->getRepository(Inscriptions::class)->findAll();
            return $this->render('main/home.html.twig', ['sorties'=>$sortie,  'campus' => $campusRepository->findAll(),'participants' => $inscriptions,]);

             }else{
             return $this->render('participants/login.html.twig',
            [
                'last_username' => $lastUserName,
                'error'=> $error,

            ]
        );}
    }

    //méthode de déconnexion
    /**
     * @Route("/deconnexion", name="logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}