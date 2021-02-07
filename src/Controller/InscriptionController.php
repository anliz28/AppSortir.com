<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Sortie;
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

        //je récupère ma sortie
        $sortie = $em->getRepository(Sortie::class)->find($id);

        //je compte le nombre d'inscrits à la sortie avec ma requête queryBuilder
        $inscription = $em->getRepository(Inscriptions::class);
        $inscrits = $inscription->countByInscrits($sortie);

        //je vérifie en bdd si mon user est déjà inscrit
        $participants = $this->getUser();
        $estInscrit = $em->getRepository(Inscriptions::class);
        $result = $estInscrit->siDejaInscrit($participants, $sortie);

        //si il est inscrit, j'affiche un message
        if ($result) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette sortie');
            return $this->render("sortie/detail.html.twig",[ 'participants'=>$participants,'sortie'=>$sortie]);
            //ou si inscriptions pleine, j'affiche un message
        } elseif ($nbMaxInscription > $sortie->getNbInscriptionsMax()) {
            $this->addFlash('error', "Impossible de s'inscrire à cette sortie, le nombre de participants est déjà au maximum de ses capacités!");
            return $this->render("sortie/detail.html.twig",[ 'participants'=>$participants,'sortie'=>$sortie]);

        } //sinon, si il reste de la place, je l'inscris
            $sortie = $em->getRepository(Sortie::class)->find($id);

            $inscription = new Inscriptions();
            $inscription->setSortie($sortie);
            $inscription->setParticipant($this->getUser());
            $inscription->setDateInscription(new \DateTime());

            $em->persist($inscription);
            $em->flush();

        $this->addFlash('success', "L'inscription' a bien été créée");
        return $this->render("sortie/list.html.twig", ['inscription' => $inscription, 'sortie'=>$sortie]);
    }


}
