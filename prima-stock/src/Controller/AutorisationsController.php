<?php

namespace App\Controller;

use App\Entity\Autorisations;
use App\Form\AutorisationsType;
use App\Repository\AutorisationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/autorisations")
 */
class AutorisationsController extends AbstractController
{
    /**
     * @Route("/", name="autorisations_index", methods={"GET"})
     */
    public function index(AutorisationsRepository $autorisationsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $autorisationsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('autorisations/index.html.twig', [
            'autorisations' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="autorisations_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $autorisation = new Autorisations();
        $form = $this->createForm(AutorisationsType::class, $autorisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($autorisation);
            $entityManager->flush();

            return $this->redirectToRoute('autorisations_index');
        }

        return $this->render('autorisations/new.html.twig', [
            'autorisation' => $autorisation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="autorisations_show", methods={"GET"})
     */
    public function show(Autorisations $autorisation): Response
    {
        return $this->render('autorisations/show.html.twig', [
            'autorisation' => $autorisation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="autorisations_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Autorisations $autorisation): Response
    {
        $form = $this->createForm(AutorisationsType::class, $autorisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('autorisations_index');
        }

        return $this->render('autorisations/edit.html.twig', [
            'autorisation' => $autorisation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="autorisations_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Autorisations $autorisation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$autorisation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($autorisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('autorisations_index');
    }
}
