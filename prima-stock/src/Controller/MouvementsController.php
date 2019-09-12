<?php

namespace App\Controller;

use App\Entity\Mouvements;
use App\Form\MouvementsType;
use App\Repository\MouvementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/mouvements")
 */
class MouvementsController extends AbstractController
{
    /**
     * @Route("/", name="mouvements_index", methods={"GET"})
     */
    public function index(MouvementsRepository $mouvementsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $mouvementsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('mouvements/index.html.twig', [
            'mouvements' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="mouvements_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mouvement = new Mouvements();
        $form = $this->createForm(MouvementsType::class, $mouvement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mouvement);
            $entityManager->flush();

            return $this->redirectToRoute('mouvements_index');
        }

        return $this->render('mouvements/new.html.twig', [
            'mouvement' => $mouvement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mouvements_show", methods={"GET"})
     */
    public function show(Mouvements $mouvement): Response
    {
        return $this->render('mouvements/show.html.twig', [
            'mouvement' => $mouvement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mouvements_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mouvements $mouvement): Response
    {
        $form = $this->createForm(MouvementsType::class, $mouvement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mouvements_index');
        }

        return $this->render('mouvements/edit.html.twig', [
            'mouvement' => $mouvement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mouvements_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mouvements $mouvement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mouvement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mouvement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mouvements_index');
    }
}
