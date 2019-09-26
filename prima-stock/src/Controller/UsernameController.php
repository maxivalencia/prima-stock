<?php

namespace App\Controller;

use App\Entity\Username;
use App\Form\UsernameType;
use App\Repository\UsernameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/username")
 */
class UsernameController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    

    /**
     * @Route("/", name="username_index", methods={"GET"})
     */
    public function index(UsernameRepository $usernameRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $dossierpaginer = $paginator->paginate(
            $usernameRepository->findAll(),
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );

        return $this->render('username/index.html.twig', [
            'usernames' => $dossierpaginer,
        ]);
    }

    /**
     * @Route("/new", name="username_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $username = new Username();
        $form = $this->createForm(UsernameType::class, $username);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $username->setPassword($this->passwordEncoder->encodePassword(
                $username,
                $username->getPassword()
            ));
            $entityManager->persist($username);
            $entityManager->flush();

            return $this->redirectToRoute('username_index');
        }

        return $this->render('username/new.html.twig', [
            'username' => $username,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="username_show", methods={"GET"})
     */
    public function show(Username $username): Response
    {
        return $this->render('username/show.html.twig', [
            'username' => $username,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="username_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Username $username): Response
    {
        $form = $this->createForm(UsernameType::class, $username);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('username_index');
        }

        return $this->render('username/edit.html.twig', [
            'username' => $username,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="username_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Username $username): Response
    {
        if ($this->isCsrfTokenValid('delete'.$username->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($username);
            $entityManager->flush();
        }

        return $this->redirectToRoute('username_index');
    }
}
