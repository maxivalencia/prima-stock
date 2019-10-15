<?php

namespace App\Controller;

use App\Entity\TypeProduits;
use App\Form\TypeProduitsType;
use App\Repository\TypeProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/type/produits")
 */
class TypeProduitsController extends AbstractController
{
    /**
     * @Route("/", name="type_produits_index", methods={"GET"})
     */
    public function index(TypeProduitsRepository $typeProduitsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $typeProduitsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('type_produits/index.html.twig', [
            'type_produits' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="type_produits_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $typeProduit = new TypeProduits();
        $form = $this->createForm(TypeProduitsType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeProduit);
            $entityManager->flush();

            return $this->redirectToRoute('type_produits_index');
        }

        return $this->render('type_produits/new.html.twig', [
            'type_produit' => $typeProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_produits_show", methods={"GET"})
     */
    public function show(TypeProduits $typeProduit): Response
    {
        return $this->render('type_produits/show.html.twig', [
            'type_produit' => $typeProduit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_produits_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeProduits $typeProduit): Response
    {
        $form = $this->createForm(TypeProduitsType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_produits_index');
        }

        return $this->render('type_produits/edit.html.twig', [
            'type_produit' => $typeProduit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_produits_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeProduits $typeProduit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeProduit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_produits_index');
    }
}
