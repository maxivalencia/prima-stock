<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Stocks;
use App\Entity\User;
use App\Entity\Mouvements;
use App\Entity\Etats;
use App\Entity\Unites;
use App\Entity\Conversions;
use App\Form\NouveauType;
use App\Form\ModifierType;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use App\Repository\MouvementsRepository;
use App\Repository\EtatsRepository;
use App\Repository\UnitesRepository;
use App\Repository\ConversionsRepository;
use Knp\Component\Pager\PaginatorInterface;

class GestionStocksController extends AbstractController
{
    /**
     * @Route("/gestion/stocks", name="gestion_stocks")
     */
    public function index()
    {
        return $this->render('gestion_stocks/index.html.twig', [
            'controller_name' => 'GestionStocksController',
        ]);
    }


    /**
     * @Route("/gestion/nouveau", name="nouveau", methods={"GET","POST"})
     */
    public function nouveau(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $stock = new Stocks;
        $form = $this->createForm(NouveauType::class, $stock);
        $form->handleRequest($request);
        $reference = $form->get('referencePanier')->getData();
        if($reference == ''){
            $daty   = new \DateTime(); //this returns the current date time
            $results = $daty->format('Y-m-d-H-i-s');
            $krr    = explode('-', $results);
            $results = implode("", $krr);
            $stock->setReferencePanier($results);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $stock->setDateSaisie(new \DateTime());
            $stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/nouveau.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/gestion/validations", name="validations", methods={"GET","POST"})
     */
    public function validations(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $stock = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);

        $pagination = $paginator->paginate(
            $stocksRepository->findByGroup($etat->getId()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/validations.html.twig',[
            'stocks' => $pagination,
        ]);
    }


    /**
     * @Route("/gestion/saisies", name="saisies", methods={"GET","POST"})
     */
    public function saisies(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $stock = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de modification"]);

        $pagination = $paginator->paginate(
            $stocksRepository->findByGroup($etat->getId()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/saisies.html.twig',[
            'stocks' => $pagination,
        ]);
    }


    /**
     * @Route("/gestion/validation/{ref}", name="validation", methods={"GET","POST"})
     */
    public function validation(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $reference = $ref;
        //$stock_restant[] = new float();
        // la solution pourrait-être une array collection
        $pagination = $paginator->paginate(
            $stocksRepository->findBy(["referencePanier" => $reference]), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        /* foreach($pagination as $page){
            $stock_restant[] = reste($page->getProduit(), $page->getProjet());
        } */
        return $this->render('gestion_stocks/validation.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            //'rest' => $stock_restant,
        ]);
    }


    /**
     * @Route("/gestion/valider/{ref}", name="valider", methods={"GET","POST"})
     */
    public function valider(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $etat2 = new Etats();
        $stock = new Stocks();
        $stock2 [] = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $etat2 = $etatsRepository->findOneBy(["etat" => "valider"]);
        $stock2[] = $stocksRepository->findBy(["referencePanier" => $ref]);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($stocksRepository->findBy(["referencePanier" => $ref]) as $sto){
            $sto->setEtat($etat2);
            $sto->setDateValidation(new \DateTime());
            $entityManager->persist($sto);
        }
        $entityManager->flush();
        
        $reference = $ref;
        $pagination = $paginator->paginate(
            $stocksRepository->findByGroup($etat->getId()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/validations.html.twig',[
            'stocks' => $pagination,
        ]);
    }


    /**
     * @Route("/gestion/annuler/{ref}", name="annuler", methods={"GET","POST"})
     */
    public function annuler(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $etat2 = new Etats();
        $stock = new Stocks();
        $stock2 [] = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $etat2 = $etatsRepository->findOneBy(["etat" => "annuler"]);
        $stock2[] = $stocksRepository->findBy(["referencePanier" => $ref]);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($stocksRepository->findBy(["referencePanier" => $ref]) as $sto){
            $sto->setEtat($etat2);
            $entityManager->persist($sto);
        }
        $entityManager->flush();
        
        $reference = $ref;
        $pagination = $paginator->paginate(
            $stocksRepository->findByGroup($etat->getId()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/validations.html.twig',[
            'stocks' => $pagination,
        ]);
    }


    /**
     * @Route("/gestion/modifier/{ref}", name="modifier", methods={"GET","POST"})
     */
    public function modifier(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $etat2 = new Etats();
        $stock = new Stocks();
        $stock2 [] = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $etat2 = $etatsRepository->findOneBy(["etat" => "en attente de modification"]);
        $stock2[] = $stocksRepository->findBy(["referencePanier" => $ref]);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($stocksRepository->findBy(["referencePanier" => $ref]) as $sto){
            $sto->setEtat($etat2);
            $entityManager->persist($sto);
        }
        $entityManager->flush();
        return $this->redirectToRoute('validations');
    }


    /**
     * @Route("/gestion/saisie/{ref}", name="saisie", methods={"GET","POST"})
     */
    public function saisie(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $reference = $ref;
        $pagination = $paginator->paginate(
            $stocksRepository->findBy(["referencePanier" => $reference]), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/saisie.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
        ]);
    }

    /**
     * @Route("/{id}/modif", name="modif", methods={"GET","POST"})
     */
    public function modif(Request $request, Stocks $stock): Response
    {
        $form = $this->createForm(ModifierType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('saisie', ['ref' => $stock->getReferencePanier()]);
        }

        return $this->render('gestion_stocks/modifier.html.twig', [
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/gestion/revalider/{ref}", name="revalider", methods={"GET","POST"})
     */
    public function revalider(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $etat = new Etats();
        $etat2 = new Etats();
        $stock = new Stocks();
        $stock2 [] = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $etat2 = $etatsRepository->findOneBy(["etat" => "en attente de modification"]);
        $stock2[] = $stocksRepository->findBy(["referencePanier" => $ref]);
        $entityManager = $this->getDoctrine()->getManager();
        foreach($stocksRepository->findBy(["referencePanier" => $ref]) as $sto){
            $sto->setEtat($etat);
            $entityManager->persist($sto);
        }
        $entityManager->flush();
        return $this->redirectToRoute('saisies');
    }


    /**
     * @Route("/gestion/historiques", name="historiques", methods={"GET","POST"})
     */
    public function historiques(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $pagination = $paginator->paginate(
            $stocksRepository->findProduction(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/historiques.html.twig',[
            'stocks' => $pagination,
        ]);
    }


    /**
     * @Route("/gestion/historiques/{ref}", name="historiques_details", methods={"GET","POST"})
     */
    public function historiques_details(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $reference = $ref;
        $pagination = $paginator->paginate(
            $stocksRepository->findGroupValidation($reference ), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/historiques_details.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
        ]);
    }

    //calculateur de tolat de reste de produit
    private function reste(Produit $prod, Projet $proj = null, MouvementsRepository $mouvementsRepository, StocksRepository $stocksRepository, UnitesRepository $unitesRepository, ConversionsRepository $conversionsRepository): float
    {
        $mouvement_positif = new Mouvements();
        $mouvement_negatif = new Mouvements();
        $unite = new Unites();
        $mouvement_positif =$mouvementsRepository->findBy(["type" => "ENTRER"]);
        $mouvement_negatif =$mouvementsRepository->findBy(["type" => "SORTIE"]);
        $total = 0;
        $valeur_unite_bas = 0;
        $unite_bas;
        foreach($stocksRepository->findTotal($prod, $proj) as $stock){
            $unite = $stock->getUnite();
            foreach($conversionsRepository->findby(["unitesource" => $unite]) as $conversion){
                if($valeur_unite_bas < $conversion->getValeur()){
                    $valeur_unite_bas = $conversion->getValeur();
                }
            }
            
            if($stock->getMouvement() == $mouvement_positif){
                $total = $total + ($stock->getQuantite * $valeur_unite_bas);
            }

            
            if($stock->getMouvement() == $mouvement_negatif){
                $total = $total - ($stock->getQuantite * $valeur_unite_bas);
            }
        }
        //findTotal()
        //$positif = 0;
        //$negatif = 0;
        //$results = $positif - $negatif;
        return $total;
    }
}
