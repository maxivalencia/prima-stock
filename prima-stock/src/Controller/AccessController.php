<?php

namespace App\Controller;

use App\Entity\Access;
use App\Form\AccessType;
use App\Repository\AccessRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/access")
 */
class AccessController extends AbstractController
{
    /**
     * @Route("/", name="access_index", methods={"GET"})
     */
    public function index(AccessRepository $accessRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $accessRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('access/index.html.twig', [
            'accesses' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="access_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $access = new Access();
        $form = $this->createForm(AccessType::class, $access);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($access);
            $entityManager->flush();

            return $this->redirectToRoute('access_index');
        }

        return $this->render('access/new.html.twig', [
            'access' => $access,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="access_show", methods={"GET"})
     */
    public function show(Access $access): Response
    {
        return $this->render('access/show.html.twig', [
            'access' => $access,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="access_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Access $access): Response
    {
        $form = $this->createForm(AccessType::class, $access);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('access_index');
        }

        return $this->render('access/edit.html.twig', [
            'access' => $access,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="access_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Access $access): Response
    {
        if ($this->isCsrfTokenValid('delete'.$access->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($access);
            $entityManager->flush();
        }

        return $this->redirectToRoute('access_index');
    }
}
