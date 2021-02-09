<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Entity\Sortie;
use App\Form\ModifPaticipantType;
use Doctrine\ORM\EntityManagerInterface;
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
//méthode qui affiche notre profil participant
    /**
     * @Route("participants/profil", name="profil")
     */
    public function readProfil()
    {
       // $participant = $em->getRepository(Participants::class)->find($id);, 'participants'=>$participant /{id} EntityManagerInterface $em, $id
        return $this->render('participants/profil.html.twig', ['participant' => $this->getUser()]);
    }

//méthode qui modifie les données de notre profil
/**
 * @Route("participants/modifProfil", name="modifProfil")
 */
    public function UpdateProfil(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $participant = $this->getUser();
        $modifForm = $this->createForm(ModifPaticipantType::class, $participant);
        $modifForm->handleRequest($request);



        if ($modifForm->isSubmitted() && $modifForm->isValid()){

            //Récupération de la photo de profil

            $picture = $modifForm->get('photoProfil')->getData();
            if($picture) {
                $fichier = $participant->getPseudo() . '.' . $picture->guessExtension();

                // On copie le fichier dans le dossier public/media
                $picture->move('media', $fichier);
                $photo = '/media/' . $fichier;
                //et on enregistre le chemin de la photo en bdd
                $participant->setPhotoProfil($photo);
            }
            //remplacement du mot de passe si modifié ou garde l'ancien
            $mdp = $participant->getPlainPassword();
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

    //Méthode qui affiche le profil des participants d'une sortie
    /**
     * @Route("participants/profilParticipant/{id}", name="profilParticipant")
     */
    public function profilParticipant( EntityManagerInterface $em, $id)
    {
        $participant = $em->getRepository(Participants::class)->find($id);
        return $this->render('participants/profilParticipant.html.twig', ['participant'=>$participant ]);
    }

}