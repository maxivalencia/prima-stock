<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Form\EtatsType;
use App\Repository\EtatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/etats")
 */
class EtatsController extends AbstractController
{
    /**
     * @Route("/", name="etats_index", methods={"GET"})
     */
    public function index(EtatsRepository $etatsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $etatsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('etats/index.html.twig', [
            'etats' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="etats_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $etat = new Etats();
        $form = $this->createForm(EtatsType::class, $etat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($etat);
            $entityManager->flush();

            return $this->redirectToRoute('etats_index');
        }

        return $this->render('etats/new.html.twig', [
            'etat' => $etat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etats_show", methods={"GET"})
     */
    public function show(Etats $etat): Response
    {
        return $this->render('etats/show.html.twig', [
            'etat' => $etat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="etats_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etats $etat): Response
    {
        $form = $this->createForm(EtatsType::class, $etat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etats_index');
        }

        return $this->render('etats/edit.html.twig', [
            'etat' => $etat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etats_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Etats $etat): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($etat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('etats_index');
    }
}
