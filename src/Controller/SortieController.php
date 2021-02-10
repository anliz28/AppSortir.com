<?php

namespace App\Controller;


use App\Entity\Inscriptions;
use App\Entity\Sortie;
use App\Form\SearchType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/list", name="home")
     */

    public function listSortie ()
    {

        $em = $this->getDoctrine()->getManager();
        return $this->render('sortie/list.html.twig',['sorties' => $em->getRepository(Sortie::class)->findAll()]);

    }
/*

    public function listSortie (SortieRepository $repository, Request $request)
    {
        $sortie = new Sortie();
        $form = $this->createForm(SearchType::class, $sortie);
        $form -> handleRequest ($request);
        $sortie = $repository->findSearch($sortie);

        return $this->render('sortie/list.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView()
        ]);
    }
    */
    //méthode pour créer une nouvelle sortie
    /**
     * @Route("sortie/add", name="sortie_add")
     */
    public function add(EntityManagerInterface $em, Request $request): Response
    {


        $sortie = new Sortie();

        //hydrater la sortie avec l'organisateur (user connecté)
        $organisateur = $this->getUser();
        $sortie->setOrganisateur($organisateur);

        $sortieAddForm = $this->createForm(SortieType::class, $sortie);
        $sortieAddForm->handleRequest($request);

        if ($sortieAddForm->isSubmitted() && $sortieAddForm->isValid()) {
            $sortie->setEtat(1);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute("home");

        }
        return $this->render('sortie/add.html.twig', [
            "sortieAddForm" => $sortieAddForm->createView()
        ]);
    }

    //méthode pour afficher les détails d'une sortie
    /**
     * @Route("sortie/detail/{id}", name="sortie_detail", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function detail($id, Request $request): Response
    {
        //récupérer la sortie en bdd
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if(empty($sortie)){
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        //Fait le lien entre l'entité inscription pour récupérer mes participants
        $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

        $participants = [];
        foreach ($inscriptions as $inscription) {
            array_push($participants, $inscription->getParticipant());
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie,
            'participants'=> $participants]);

    }

    //méthode pour modifier une sortie
    /**
     * @Route("sortie/modifier/{id}", name="sortie_modifier", requirements={"id":"\d+"})
     */
    public function modifier($id, Request $request, EntityManagerInterface $em): Response
    {

        //récupérer la sortie en bdd
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if (empty($sortie)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        //contrôler que le user est bien l'organisateur
        $organisateur_id = $sortie->getOrganisateur()->getId();

        //récupérer le user connecté
        $user_id = $this->getUser()->getId();

        if ($organisateur_id <> $user_id) {
            //récupérer la liste des participants
            //Fait le lien entre l'entité inscription pour récupérer mes participants
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }

            $this->addFlash('error', "Vous devez être l'organisateur de cette sortie pour pouvoir la modifier");
            return $this->render('sortie/detail.html.twig', [
                "sortie" => $sortie,
                'participants'=> $participants
                ]);
        } else {

            //récupérer les infos de la sortie sélectionnée
            $sortieModifForm = $this->createForm(SortieType::class, $sortie);

            $sortieModifForm->handleRequest($request);

            if ($sortieModifForm->isSubmitted() && $sortieModifForm->isValid()) {

                $sortie = $sortieModifForm->getData();
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'La sortie a bien été modifiée');
                return $this->redirectToRoute("home");
            }
            //récupérer la liste des participants
            //Fait le lien entre l'entité inscription pour récupérer mes participants
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }


            return $this->render('sortie/modifier.html.twig', [
                "sortieModifForm" => $sortieModifForm->createView(),
                "sortie" => $sortie, 'participants'=> $participants
            ]);
        }
    }

   // méthode pour modifier l'état d'une sortie, en "publiée"
    /**
     * @Route("sortie/publier/{id}", name="sortie_publier", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function publier($id, EntityManagerInterface $em): Response
    {
        //recherche en bdd
        $serieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $serieRepo->find($id);

        if (empty($sortie)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        //contrôler que le user est bien l'organisateur
        $organisateur_id = $sortie->getOrganisateur()->getId();

        //récupérer le user connecté
        $user_id = $this->getUser()->getId();

        if ($organisateur_id <> $user_id) {
            //récupérer la liste des participants
            //Fait le lien entre l'entité inscription pour récupérer mes participants
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }

            $this->addFlash('error', "Vous devez être l'organisateur de cette sortie pour pouvoir la publier");
            return $this->render('sortie/detail.html.twig', [
                "sortie" => $sortie,
                'participants' => $participants
            ]);
        } else {
            //modification de l'etat
            $sortie->setEtat(2);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été publiée');
            return $this->render('main/home.html.twig', [
                "sortie" => $sortie
            ]);
        }
    }

    //méthode pour modifier l'état d'une sortie en "annulée"
    /**
     * @Route("sortie/annuler/{id}", name="sortie_annuler", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function annuler($id, EntityManagerInterface $em): Response
    {
        //recherche en bdd
        $serieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $serieRepo->find($id);

        if (empty($sortie)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        //contrôler que le user est bien l'organisateur
        $organisateur_id = $sortie->getOrganisateur()->getId();


        //récupérer le user connecté
        $user_id = $this->getUser()->getId();

        return $this->render('main/home.html.twig', [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/", name="sortie")
     */

    /**
     * @Route("sortie/delete/{id}", name="sortie_delete", requirements={"id":"\d+"}, methods={"GET"})
>>>>>>> filtres

        $dateDebut = $sortie->getDateDebut();
        $dateDuJour = new \DateTime();

        if ($organisateur_id <> $user_id) {
            //récupérer la liste des participants
            //Fait le lien entre l'entité inscription pour récupérer mes participants
            $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

            $participants = [];
            foreach ($inscriptions as $inscription) {
                array_push($participants, $inscription->getParticipant());
            }

            $this->addFlash('error', "Vous devez être l'organisateur de cette sortie pour pouvoir l'annuler");
            return $this->render('sortie/detail.html.twig', [
                "sortie" => $sortie,
                'participants' => $participants
            ]);
        } elseif ($dateDebut < $dateDuJour) {
            //contrôler si la sortie n'est pas déjà démarrée

                $inscriptions = $this->getDoctrine()->getRepository(Inscriptions::class)->findBySortie($sortie);

                $participants = [];
                foreach ($inscriptions as $inscription) {
                    array_push($participants, $inscription->getParticipant());
                }

                $this->addFlash('error', "Vous ne pouvez pas annuler cette sortie car elle est déjà commencée");
                return $this->render('sortie/detail.html.twig', [
                    "sortie" => $sortie,
                    'participants' => $participants
                ]);
            } else {

                $sortie->setEtat(6);
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'La sortie a bien été annulée');

                return $this->render('main/home.html.twig', [
                    "sortie" => $sortie
                ]);
            }

    }
        /**
         * @Route("sortie/delete/{id}", name="sortie_delete", requirements={"id":"\d+"}, methods={"GET"})
        */
    public function delete($id, EntityManagerInterface $em): Response
    {
        //recherche en bdd
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        //suppression
        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', "La sortie a été supprimée");

        return $this->render('main/home.html.twig');

    }


    //méthode pour afficher la liste des sorties
 /*
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();
//dd('hello');
        return $this->render('sortie/list.html.twig',['sortie' => $em->getRepository(Sortie::class)->findAll()]);

    }
    */
}

