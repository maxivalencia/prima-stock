<?php

namespace App\Controller;

use App\Entity\Validations;
use App\Form\ValidationsType;
use App\Repository\ValidationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/validations")
 */
class ValidationsController extends AbstractController
{
    /**
     * @Route("/", name="validations_index", methods={"GET"})
     */
    public function index(ValidationsRepository $validationsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $validationsRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('validations/index.html.twig', [
            'validations' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="validations_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $validation = new Validations();
        $form = $this->createForm(ValidationsType::class, $validation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($validation);
            $entityManager->flush();

            return $this->redirectToRoute('validations_index');
        }

        return $this->render('validations/new.html.twig', [
            'validation' => $validation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="validations_show", methods={"GET"})
     */
    public function show(Validations $validation): Response
    {
        return $this->render('validations/show.html.twig', [
            'validation' => $validation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="validations_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Validations $validation): Response
    {
        $form = $this->createForm(ValidationsType::class, $validation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('validations_index');
        }

        return $this->render('validations/edit.html.twig', [
            'validation' => $validation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="validations_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Validations $validation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$validation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($validation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('validations_index');
    }
}
