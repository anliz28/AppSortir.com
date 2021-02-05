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
        $sortieAddForm = $this->createForm(SortieType::class, $sortie);
        $sortieAddForm->handleRequest($request);

        if ($sortieAddForm->isSubmitted() && $sortieAddForm->isValid()) {

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute("list");
        }
        return $this->render('sortie/add.html.twig', [
            "sortieAddForm" => $sortieAddForm->createView()
        ]);
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

