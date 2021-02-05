<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/add", name="sortie_add")
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

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute("home");

        }
        return $this->render('sortie/add.html.twig', [
            "sortieAddForm" => $sortieAddForm->createView()
        ]);
    }
    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function detail($id, Request $request): Response
    {
        //récupérer la sortie en bdd
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if(empty($sortie)){
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie
        ]);

    }

    /**
     * @Route("/sortie/modifier/{id}", name="sortie_modifier", requirements={"id":"\d+"})
     */
    public function modifier($id, Request $request, EntityManagerInterface $em): Response
    {
        //récupérer la sortie en bdd
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if(empty($sortie)){
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        //récupérer les infos de la sortie sélectionnée
        $sortieModifForm = $this->createForm(SortieType::class, $sortie);

        $sortieModifForm->handleRequest($request);

        if ($sortieModifForm->isSubmitted() && $sortieModifForm->isValid()) {
            //TODO récupérer les nouvelles infos et les associer à ma $sortie ?
            $sortie = $sortieModifForm->getData();
            dump($sortie);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée');
            return $this->redirectToRoute("home");
        }

        return $this->render('sortie/modifier.html.twig', [
            "sortieModifForm" => $sortieModifForm->createView(),
            "sortie" => $sortie
        ]);


    }


    /**
     * @Route("/sortie/publier/{id}", name="sortie_publier", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function publier($id, EntityManagerInterface $em): Response
    {
        //recherche en bdd
        $serieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $serieRepo->find($id);

        if(empty($sortie)){
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        //récupérer les nouvelles infos
        //$sortieModifForm = $this->createForm(SortieType::class, $sortie);
        //$sortieModifForm->handleRequest($request);

       //if ($sortieModifForm->isSubmitted() && $sortieModifForm->isValid()) {

            //modification de l'etat
            $sortie->setEtat(2);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée');
        //    return $this->redirectToRoute("home");
       // }
        return $this->render('main/home.html.twig', [
           // "sortieModifForm" => $sortieModifForm->createView(),
            "sortie" => $sortie
        ]);
    }
    /**
     * @Route("/sortie/annuler/{id}", name="sortie_annuler", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function annuler($id, EntityManagerInterface $em): Response
    {
        //recherche en bdd
        $serieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $serieRepo->find($id);


        if(empty($sortie)){
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }


       // $sortieModifForm = $this->createForm(SortieType::class, $sortie);
      //  $sortieModifForm->handleRequest($request);

       // if ($sortieModifForm->isSubmitted() && $sortieModifForm->isValid()) {

            //modification de l'etat
            $sortie->setEtat(6);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée');
         //  return $this->redirectToRoute("home");
      //  }
        return $this->render('main/home.html.twig', [
          //  "sortieModifForm" => $sortieModifForm->createView(),
            "sortie" => $sortie
        ]);
    }
    /**
     * @Route("/sortie/delete/{id}", name="sortie_delete", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function delete($id, EntityManagerInterface $em): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('sortie/list.html.twig',['sortie' => $em->getRepository(Sortie::class)->findAll()]);

        //recherche en bdd
        $serieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $serieRepo->find($id);

        //suppression
        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', "La sortie a été supprimée");

        return $this->render('main/home.html.twig');

    }
    /**
     * @Route("sortie/list", name="list")
     */
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('sortie/list.html.twig',['sortie' => $em->getRepository(Sortie::class)->findAll()]);

    }
}

