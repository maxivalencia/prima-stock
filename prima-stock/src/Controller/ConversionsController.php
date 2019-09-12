<?php

namespace App\Controller;

use App\Entity\Conversions;
use App\Form\ConversionsType;
use App\Repository\ConversionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/conversions")
 */
class ConversionsController extends AbstractController
{
    /**
     * @Route("/", name="conversions_index", methods={"GET"})
     */
    public function index(ConversionsRepository $conversionsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $conversionsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('conversions/index.html.twig', [
            'conversions' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="conversions_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $conversion = new Conversions();
        $form = $this->createForm(ConversionsType::class, $conversion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conversion);
            $entityManager->flush();

            return $this->redirectToRoute('conversions_index');
        }

        return $this->render('conversions/new.html.twig', [
            'conversion' => $conversion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="conversions_show", methods={"GET"})
     */
    public function show(Conversions $conversion): Response
    {
        return $this->render('conversions/show.html.twig', [
            'conversion' => $conversion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="conversions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Conversions $conversion): Response
    {
        $form = $this->createForm(ConversionsType::class, $conversion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conversions_index');
        }

        return $this->render('conversions/edit.html.twig', [
            'conversion' => $conversion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="conversions_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Conversions $conversion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conversion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conversion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conversions_index');
    }
}
