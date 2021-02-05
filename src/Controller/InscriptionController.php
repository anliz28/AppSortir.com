<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participants;
use App\Entity\Sortie;
use App\Form\InscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class InscriptionController extends AbstractController
{
    /**
     * @Route("sortie/inscrire/{id}", name="inscrire")
     */
    public function addInscription($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Sortie $sortie
         */
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $inscription = new Inscriptions();
        $inscription->setSortie($sortie);
        $inscription->setParticipant($this->getUser());
        $inscription->setDateInscription(new \DateTime());

        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', "L'inscription' a bien été créée");
        return $this->render("main/home.html.twig",['inscription'=>$inscription]);
    }


}
