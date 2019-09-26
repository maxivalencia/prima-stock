<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Username;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\UsernameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $username = new Username();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $username->setUsername($user->getLogin());
            $username->setPassword($user->getPassword());
            $username->setAccess($user->getAccess());
            $username->setRoles(['ROLE_'.$user->getAccess()]);
            /* $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            )); */
            $username->setPassword($this->passwordEncoder->encodePassword(
                $username,
                $username->getPassword()
            ));
            $user->setPassword("ce n'est pas ici que le mot de passe se trouve");
            $entityManager->persist($username);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $username = new Username();
        //$usernameRepository = new UserRepository();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* $this->getDoctrine()->getManager()->flush(); */
            $entityManager = $this->getDoctrine()->getManager();
            $usernameRepository = $entityManager->getRepository(Username::class);
            $username = $usernameRepository->findOneBy(["username" => $user->getLogin()]);
            $username->setUsername($user->getLogin());
            $username->setPassword($user->getPassword());
            $username->setAccess($user->getAccess());
            $username->setRoles(['ROLE_'.$user->getAccess()]);
            /* $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            )); */
            $username->setPassword($this->passwordEncoder->encodePassword(
                $username,
                $username->getPassword()
            ));
            $user->setPassword("ce n'est pas ici que le mot de passe se trouve");
            $entityManager->persist($username);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
