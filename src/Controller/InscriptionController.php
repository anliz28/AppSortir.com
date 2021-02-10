<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class InscriptionController extends AbstractController
{

    //méthode qui permet de s'inscrire à une sortie si il reste de la place,
    // et si on est pas encore inscrit!
    /**
     * @Route("sortie/inscrire/{id}", name="inscrire")
     */
    public function addInscription($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $nbMaxInscription = 0;

        //je récupère mon user courant
        $participant = $this->getUser();

        //je récupère ma sortie et sa date de cloture
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $clotureSortie = $sortie->getDateCLoture();

        //contrôle de l'ouverture de la sortie aux inscription (publier donc etat=2)
        if($sortie->getEtat() <>2){
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }
            $this->addFlash('error', 'Cette sortie n\'est pas ouverte aux inscirptions');
            return $this->render("sortie/detail.html.twig", ['participants' => $participants, 'sortie' => $sortie]);
        }
        //je compte le nombre d'inscrits à la sortie avec ma requête queryBuilder
        $inscription = $em->getRepository(Inscriptions::class);
        $inscrits = $inscription->countByInscrits($sortie);

        //je vérifie en bdd si mon user est déjà inscrit
        $participants = $this->getUser();
        $estInscrit = $em->getRepository(Inscriptions::class);
        $result = $estInscrit->siDejaInscrit($participants, $sortie);

        //si il est inscrit, j'affiche un message
        if ($result) {
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette sortie');
            return $this->render("sortie/detail.html.twig", ['participants' => $participants, 'sortie' => $sortie]);
            //ou si inscriptions pleine, j'affiche un message
        } elseif ($nbMaxInscription == $sortie->getNbInscriptionsMax()) {
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }
                $this->addFlash('error', "Impossible de s'inscrire à cette sortie, le nombre de participants est déjà au maximum de ses capacités!");
                return $this->render("sortie/detail.html.twig", ['participants' => $participants, 'sortie' => $sortie]);

            }
            //sinon, je vérifie que la date de cloture n'est pas dépassée
            $dateDuJour = new \DateTime();

            if ($clotureSortie < $dateDuJour) {
                $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
                $participants = [];
                foreach ($inscriptions as $inscription) {
                    array_push($participants, $inscription->getParticipant());
                }
                $this->addFlash('error', "Dommage ! Les inscriptions pour cette sortie sont cloturées. Une autre fois peut-être ?");
                return $this->render("sortie/detail.html.twig", ['participants' => $participants, 'sortie' => $sortie]);
            } else {
                //sinon je l'inscris

                $sortie = $em->getRepository(Sortie::class)->find($id);

                $inscription = new Inscriptions();
                $inscription->setSortie($sortie);
                $inscription->setParticipant($this->getUser());
                $inscription->setDateInscription(new \DateTime());

                $em->persist($inscription);
                $em->flush();

                $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
                $participants = [];
                foreach ($inscriptions as $inscription) {
                    array_push($participants, $inscription->getParticipant());
                    $this->addFlash('success', "L'inscription' a bien été créée");
                    return $this->render("sortie/detail.html.twig", ['inscription' => $inscription, 'sortie' => $sortie, 'participants' => $participants]);
                }
            }

    }

    /**
     * @Route ("sortie/desinscrire/{id}", name="sortie_desinscrire")
     */
    public function desistement(EntityManagerInterface $em, $id): Response
    {
        //récupération de la sortie
        $sortie = $em->getRepository(Sortie::class)->find($id);

        //récupération du user courant
        $participant = $this->getUser();
        $dateDuJour = new \DateTime();
        $dateDebut = $sortie->getDateDebut();

        //contrôle du non commencement de la sortie
        if($dateDebut < $dateDuJour){

            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);
            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
                $this->addFlash('error','Cette sortie a déjà commencé ou passée, vous ne pouvez plus vous désister');
                return $this->render("sortie/detail.html.twig", ['inscription' => $inscription, 'sortie' => $sortie, 'participants' => $participants]);
            }
        }else {

            //recherche de l'inscription à supprimer
            $inscriptionRepo = $em->getRepository(Inscriptions::class)->findBy(array("participant" => $participant, "sortie" => $sortie), [], 1, 0);

            //sélection de l'inscription

            $inscription = $inscriptionRepo[0];

            //suppression de l'inscription
            $em->remove($inscription);
            $em->flush();

            $this->addFlash('success', "Vous avez bien été désinscrit");
        }
            return $this->render("main/home.html.twig");

        }

}
