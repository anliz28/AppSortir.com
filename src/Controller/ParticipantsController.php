<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\ModifPaticipantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ParticipantsController
 * @package App\Controller
 */
class ParticipantsController extends AbstractController
{

    /**
     * @Route("participants/profil", name="profil")
     */
    public function readProfil()
    {
        return $this->render('participants/profil.html.twig', ['participant' => $this->getUser()]);
    }


/**
 * @Route("participants/modifProfil", name="modifProfil")
 */
    public function UpdateProfil(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $participant = $this->getUser();
        $modifForm = $this->createForm(ModifPaticipantType::class, $participant);
        $modifForm->handleRequest($request);

        $mdp = $participant->getPlainPassword();

        if ($modifForm->isSubmitted() && $modifForm->isValid()){
            if(empty($mdp)){
                $mdp = $participant->getPassword();
            }else{
                $mdp = $encoder->encodePassword($participant,$participant->getPlainPassword());
                $participant->setMotDePasse($mdp);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();
            return $this->redirectToRoute('profil',['participant'=>$participant]);
        }
        return $this->render('participants/modifProfil.html.twig', ['modifForm'=>$modifForm->createView()]);
    }

}