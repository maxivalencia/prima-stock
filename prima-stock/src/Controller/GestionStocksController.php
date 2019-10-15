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
use App\Entity\Produits;
use App\Entity\Projet;
use App\Entity\Clients;
use App\Entity\PieceJointe;
use App\Form\NouveauType;
use App\Form\EntrerType;
use App\Form\SortieType;
use App\Form\ModifierType;
use App\Form\ProduitsType;
use App\Form\ProjetType;
//use App\Form\PieceJointe;
//use App\Entity\PieceJointe;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use App\Repository\MouvementsRepository;
use App\Repository\EtatsRepository;
use App\Repository\UnitesRepository;
use App\Repository\ConversionsRepository;
use App\Repository\ProduitsRepository;
use App\Repository\ProjetRepository;
use App\Repository\ClientsRepository;
use App\Repository\PieceJointeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

//use Doctrine\Common\Collections\Collection;

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
    public function validation(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request, ProduitsRepository $produitsRepository, ProjetRepository $projetRepository): Response
    {
        $stock = new Stocks();
        $reference = $ref;
        $stock_restant= array();
        $i = 0;
        $client = "";
        $projet = "";
        //$report = "";
        // la solution pourrait-être une array collection
        $pagination = $paginator->paginate(
            $stocksRepository->findBy(["referencePanier" => $reference]), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        foreach($pagination as $page){
            $stock_restant[$i] = $this->reste($page);
            //$report = $report.' | le reste du produit '.$page->getProduit().' '.$this->reste($page);
            $client = $page->getClient();
            $projet = $page->getProjet();
            $i++;
        }
        return $this->render('gestion_stocks/validation.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            'rests' => $stock_restant,
            'client' => $client,
            'projet' => $projet,
        ]);
    }


    /**
     * @Route("/gestion/valider/{ref}", name="valider", methods={"GET","POST"})
     */
    public function valider(int $ref, UserRepository $userRepository, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
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
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $sto->setValidateur($user);
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
        $reference = '';
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $etat2 = $etatsRepository->findOneBy(["etat" => "en attente de modification"]);
        $stock2[] = $stocksRepository->findBy(["referencePanier" => $ref]);
        $entityManager = $this->getDoctrine()->getManager();
        $reference = $ref;
        foreach($stocksRepository->findBy(["referencePanier" => $ref]) as $sto){
            $sto->setEtat($etat2);
            $entityManager->persist($sto);
        }
        $entityManager->flush();
        return $this->redirectToRoute('saisie', ['ref' => $reference]);
    }


    /**
     * @Route("/gestion/saisie/{ref}", name="saisie", methods={"GET","POST"})
     */
    public function saisie(int $ref, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $stock_restant= array();
        $reference = $ref;
        $client = '';
        $projet = '';
        $i = 0;
        $pagination = $paginator->paginate(
            $stocksRepository->findBy(["referencePanier" => $reference]), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        foreach($pagination as $page){
            $stock_restant[$i] = $this->reste($page);
            //$report = $report.' | le reste du produit '.$page->getProduit().' '.$this->reste($page);
            $client = $page->getClient();
            $projet = $page->getProjet();
            $i++;
        }
        return $this->render('gestion_stocks/saisie.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            'client' => $client,
            'projet' => $projet,
            'rests' => $stock_restant,
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
        $etat = $etatsRepository->findOneBy(["etat" => "valider"]);
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
    public function historiques_details(int $ref, PieceJointeRepository $pieceJointeRepository, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $stock = new Stocks();
        $piecejointe[] = new PieceJointe();
        $reference = $ref;
        $client = "";
        $projet = "";
        $pagination = $paginator->paginate(
            $stocksRepository->findGroupValidation($reference ), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        foreach($pagination as $page){
            //$stock_restant[$i] = $this->reste($page);
            //$report = $report.' | le reste du produit '.$page->getProduit().' '.$this->reste($page);
            $client = $page->getClient();
            $projet = $page->getProjet();
            $piecejointe = $pieceJointeRepository->findBy(['reference' => $page->getPiece()]);
            //$i++;
        }
        return $this->render('gestion_stocks/historiques_details.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            'client' => $client,
            'projet' => $projet,
            'piecejointe' => $piecejointe,
        ]);
    }


    /**
     * @Route("/gestion/etat", name="etat", methods={"GET","POST"})
     */
    public function etat(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request, ProduitsRepository $produitsRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock_restant[] = new Stocks();
        $etatsRepository = $entityManager->getRepository(Etats::class);
        $etat = new Etats();
        $etat = $etatsRepository->findOneBy(["id" => 4]);
        $i = 0;
        $mouvement_positif = new Mouvements();
        $mouvement_negatif = new Mouvements();
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement_positif = $mouvementsRepository->findOneBy(["id" => 1]);
        $mouvement_negatif = $mouvementsRepository->findOneBy(["id" => 2]);
        $conversionsRepository = $entityManager->getRepository(Conversions::class);
        $total = array();
        $valeur_unite_bas = 0;
        /* foreach($produitsRepository->findAll() as $prod){
            $stock = new Stocks();
            $unite = new Unites();
            $unite_bas = '';
            foreach($stocksRepository->findBy(["produit" => $prod]) as $sto){  
                $unite = $sto->getUnite();
                if($unite_bas == ''){
                    $unite_bas = $unite->getSigle();
                }           
                if($stock == null){
                    $stock = $sto; 
                    foreach($conversionsRepository->findby(["unitesource" => $unite]) as $conversion){
                        if($valeur_unite_bas < $conversion->getValeur()){
                            $valeur_unite_bas = $conversion->getValeur();
                            $unite_bas = $conversion->getUnitesdestinataire();
                        }
                    }
                    if($valeur_unite_bas == 0){
                        $stock->setQuatite($stock->getQuantite() * $valeur_unite_bas);
                    }
                }else{ 
                    foreach($conversionsRepository->findby(["unitesource" => $unite]) as $conversion){
                        if($valeur_unite_bas < $conversion->getValeur()){
                            $valeur_unite_bas = $conversion->getValeur();
                            $unite_bas = $conversion->getUnitesdestinataire();
                        }
                    }
                    if($sto->getMouvement() == $mouvement_positif && $sto->getEtat() == $etat){
                        if($sto->getUnite() == $unite_bas){
                            $stock->setQuantite($stock->getQuantite() + $sto->getQuantite());
                        }else{
                            $stock->setQuantite($stock->getQuantite() + ($sto->getQuantite() * $valeur_unite_bas));
                        }
                    }                   
                    if($sto->getMouvement() == $mouvement_negatif && $sto->getEtat() == $etat){
                        if($stock->getUnite() == $unite_bas){
                            $stock->setQuantite($stock->getQuantite() - $sto->getQuantite());
                        }else{
                            $stock->setQuantite($stock->getQuantite() - ($sto->getQuantite() * $valeur_unite_bas));
                        }
                    }
                }
            }
            $total[$i] = new Stocks();
            $total[$i] = $stock;
            //$stock_restant[$i] = new Stocks();
            //$stock_restant[$i] = $stock;
            $i++;

        }
        $pagination = $paginator->paginate(
            $total,
            $request->query->getInt('page', 1),
            10
        ); */

        $pagination = $paginator->paginate(
            $stocksRepository->findEtat(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        foreach($pagination as $page){
            $stock_restant[$i] = $this->reste($page);
            //$report = $report.' | le reste du produit '.$page->getProduit().' '.$this->reste($page);
            $i++;
        }
        
        return $this->render('gestion_stocks/etat.html.twig',[
            'stocks' => $pagination,
            'rests' => $stock_restant,
        ]);
    }

    //calculateur de total de reste de produit
    //private function reste(Produits $prod, Projet $proj)
    private function reste($stock)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $produitsRepository = $entityManager->getRepository(Produits::class);
        $projetRepository = $entityManager->getRepository(Projet::class);
        $stocksRepository = $entityManager->getRepository(Stocks::class);
        $conversionsRepository = $entityManager->getRepository(Conversions::class);
        $etatsRepository = $entityManager->getRepository(Etats::class);
        $mouvement_positif = new Mouvements();
        $mouvement_negatif = new Mouvements();
        $unite = new Unites();
        $etat = new Etats();
        $mouvement_positif = $mouvementsRepository->findOneBy(["id" => 1]);
        $mouvement_negatif = $mouvementsRepository->findOneBy(["id" => 2]);
        $etat = $etatsRepository->findOneBy(["id" => 4]);
        $total = 0;
        $valeur_unite_bas = 0;
        $unite_bas = '';
        $autre_unite_sup = array();
        $autre_valeur_sup = array();
        $autre_unite_inf = array();
        $autre_valeur_inf = array();
        $i = 0;
        $j = 0;
        $prod = $produitsRepository->findOneBy(["id" => $stock->getProduit()]);
        $proj = $projetRepository->findOneBy(["id" => $stock->getProjet()]);
        /* if ($proj != null){
            $unite_bas = $proj->getNom();
        } */
        foreach($stocksRepository->findBy(["produit" => $prod]) as $stock){
            if($stock->getProjet() == $proj){ 
                $unite = $stock->getUnite();
                if($unite_bas == ''){
                    $unite_bas = $unite->getSigle();
                }
                foreach($conversionsRepository->findby(["unitesource" => $unite]) as $conversion){
                    if($valeur_unite_bas < $conversion->getValeur() && $conversion->getProduits() == $stock->getProduit()){
                        $valeur_unite_bas = $conversion->getValeur();
                        $unite_bas = $conversion->getUnitesdestinataire();
                        /* foreach($autre_unite_sup as $autre){
                            if($autre != $conversion->getUnitesdestinataire()){
                                $autre_unite_sup [] = $conversion->getUnitesdestinataire()->getSigle();
                                $autre_valeur_sup [] = $conversion->getValeur();
                                $i++;
                            }
                        } */
                        /* $autre_unite_sup [] = $conversion->getUnitesdestinataire()->getSigle();
                        $autre_valeur_sup [] = $conversion->getValeur(); */
                    }
                    //if($valeur_unite_bas >= $conversion->getValeur()){
                       /*  $valeur_unite_bas = $conversion->getValeur();
                        $unite_bas = $conversion->getUnitesdestinataire(); */
                        /* foreach($autre_unite_inf as $autre){
                            if($autre != $conversion->getUnitesource()){
                                $autre_unite_inf[$i] = $conversion->getUnitesource()->getSigle();
                                $autre_valeur_inf[$i] = $conversion->getValeur();
                                $j++;
                            }
                        }  */
                        /* $autre_unite_inf [] = $conversion->getUnitesdestinataire()->getSigle();
                        $autre_valeur_inf [] = $conversion->getValeur(); */
                    //}
                    // préparation du calcul des autres unité
                    /* foreach($autre_unite_sup as $autre){
                        if($autre != $conversion->getUnitesdestinataire()){
                            $autre_unite_sup [] = $conversion->getUnitesdestinataire();
                            $autre_valeur_sup [] = $conversion->getValeur();
                            $i++;
                        }
                    } *//* 
                    foreach($autre_unite_inf as $autre){
                        if($autre != $conversion->getUnitesource()){
                            $autre_unite_inf[$i] = $conversion->getUnitesource();
                            $autre_valeur_inf[$i] = $conversion->getValeur();
                            $j++;
                        }
                    } */
                    // fin de la préparation du calcul des autres unité
                }
            
                if($stock->getMouvement() == $mouvement_positif && $stock->getEtat() == $etat){
                    if($stock->getUnite() == $unite_bas){
                        $total = $total + $stock->getQuantite();
                    }else{
                        $total = $total + ($stock->getQuantite() * $valeur_unite_bas);
                    }
                }    
                
                if($stock->getMouvement() == $mouvement_negatif && $stock->getEtat() == $etat){
                    if($stock->getUnite() == $unite_bas){
                        $total = $total - $stock->getQuantite();
                    }else{
                        $total = $total - ($stock->getQuantite() * $valeur_unite_bas);
                    }
                }
            }
        }
        $k = 0;
        $convers = " ";
        foreach($conversionsRepository->findAll() as $conv){
            if(/* $conv->getUnitesource()->getSigle()==$unite_bas |  */$conv->getUnitesdestinataire()->getSigle()==$unite_bas){
                $res = $total / $conv->getValeur();
                $convers = $convers.' | '.$res.' '.$conv->getUnitesource()->getSigle();
                $k++;
            }
        }
        /* $k = 0;
        $convers = " | ";
        foreach($autre_unite_sup as $autresup){
            $res = $autre_valeur_sup[$k] * $total;
            $convers = $convers.' | '.$res.' '.$autre_unite_sup[$k];
            $k++;
        }
        $k = 0;
        foreach($autre_unite_inf as $autresup){
            $res = $autre_valeur_inf[$k] / $total;
            $convers = $convers.' | '.$res.' '.$autre_unite_inf[$k];
            $k++;
        } */
        return $total.' '.$unite_bas.' '.$convers;        
    }


    // modificaiton après présentation de l'ébauche
    // pour l'entrer des produits


    /**
     * @Route("/gestion/entrer", name="entrer", methods={"GET","POST"})
     */
    public function entrer(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock = new Stocks;
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement = $mouvementsRepository->findOneBy(["id" => 1]);
        $form = $this->createForm(EntrerType::class, $stock);
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
            //$stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $stock->setOperateur($user);
            $stock->setCauseAnnulation("Entrer standard");
            //$stock->setPiece($reference);
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        
        $daty   = new \DateTime(); //this returns the current date time
        $results = $daty->format('Y-m-d-H-i-s');
        $krr    = explode('-', $results);
        $results = implode("", $krr).$this->generateUniqueFileName();
        return $this->render('gestion_stocks/entrer.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
            'refpiecejointe' => $reference,
        ]);
    }

    // pour la sortie des produits


    /**
     * @Route("/gestion/sortie", name="sortie", methods={"GET","POST"})
     */
    public function sortie(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock = new Stocks;
        $stock2 = new Stocks;
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement = $mouvementsRepository->findOneBy(["id" => 2]);
        $form = $this->createForm(SortieType::class, $stock);
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
            //$stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $stock->setOperateur($user);
            $stock->setCauseAnnulation("Sortie standard");
            //$stock->setPiece($reference);
            $entityManager->persist($stock);
            if($stock->getAutreSource() != null){
                $stock2 = $stock->getAutreSource();
                $stock2->setQuantite($stock->getQuantite());
                $stock->setCauseAnnulation("insuffisante de stock pour :".$stock2->getCauseAnnulation());
            }
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        
        $daty   = new \DateTime(); //this returns the current date time
        $results = $daty->format('Y-m-d-H-i-s');
        $krr    = explode('-', $results);
        $results = implode("", $krr).$this->generateUniqueFileName();

        //$form->setReference($form->get('reference')->getData());
        return $this->render('gestion_stocks/sortie.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
            'refpiecejointe' => $reference,
        ]);
    }


    /**
     * @Route("/gestion/retour", name="retour", methods={"GET","POST"})
     */
    public function retour(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock = new Stocks;
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement = $mouvementsRepository->findOneBy(["id" => 1]);
        $form = $this->createForm(SortieType::class, $stock);
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
            //$stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $stock->setOperateur($user);
            $stock->setCauseAnnulation("Retour de produit non consommer");
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/retour.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/gestion/remplacement", name="remplacement", methods={"GET","POST"})
     */
    public function remplacement(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock = new Stocks;
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement = $mouvementsRepository->findOneBy(["id" => 1]);
        $form = $this->createForm(SortieType::class, $stock);
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
            //$stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $stock->setOperateur($user);
            $stock->setCauseAnnulation("Remplacement des produits avariés");
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/remplacement.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/gestion/avarie", name="avarie", methods={"GET","POST"})
     */
    public function avarie(Request $request, UserRepository $userRepository, EtatsRepository $etatsrepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stock = new Stocks;
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement = $mouvementsRepository->findOneBy(["id" => 2]);
        $form = $this->createForm(SortieType::class, $stock);
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
            //$stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $username = $this->getUser();
            $user = $userRepository->findOneBy(["login" => $username->getUsername()]);
            $stock->setOperateur($user);
            $stock->setCauseAnnulation("Retrait des produits avariés");
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/avarie.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{ref}/pdf", name="pdf", methods={"GET"})
     */
    public function pdf(int $ref, UserRepository $userRepository, StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $stock = new Stocks();
        $operateur = new User();
        $validateur = new User();
        $date_saisie;
        $date_validation;
        $reference = $ref;
        $client = "";
        $projet = "";
        $opera = $this->getUser();
        $utilisateur = $userRepository->findOneBy(["login" => $opera->getUsername()]);
        //$operateur = '';
        $pagination = $paginator->paginate(
            $stocksRepository->findGroupValidation($reference ), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        foreach($pagination as $page){
            //$stock_restant[$i] = $this->reste($page);
            //$report = $report.' | le reste du produit '.$page->getProduit().' '.$this->reste($page);
            $client = $page->getClient();
            $projet = $page->getProjet();
            $operateur = $page->getOperateur();
            $validateur = $page->getValidateur();
            $date_saisie = $page->getDateSaisie();
            $date_validation = $page->getDateValidation();
            //$i++;
        }
       /*  return $this->render('gestion_stocks/historiques_details.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            'client' => $client,
            'projet' => $projet,
        ]); */
        //$piecejointe = $piecesJointesRepository->findBy(['referencePJ' => $dossier->getPiecejointes()]);
        $date = new \DateTime();
        $logo = $this->getParameter('image').'/LOGOFINAL.GIF';
        $html = $this->renderView('gestion_stocks/pdfsaisie.html.twig', [
            'stocks' => $stocksRepository->findGroupValidation($reference ),
            //'piecejointe' => $piecejointe,
            'logo' => $logo,
            'reference' => $reference,
            'date' => $date,
            'operateur' => $operateur,
            'objet' => $projet,
            'client' => $client,
            'validateur' => $validateur,
            'datesaisie' => $date_saisie,
            'datevalidation' => $date_validation,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        //$fichier = $dossier->getObjet();
        $dompdf->stream("mouvement_".$reference.".pdf", [
            "Attachment" => true
        ]);

    }


    /**
     * @Route("/gestion/rechercher", name="rechercher", methods={"GET","POST"})
     */
    public function rechercher(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $recherche = $request->query->get('search');
        $entityManager = $this->getDoctrine()->getManager();
        $produitsRepository = $entityManager->getRepository(Produits::class);
        $projetRepository = $entityManager->getRepository(Projet::class);
        $clientsRepository = $entityManager->getRepository(Clients::class);
        $produits1 = $produitsRepository->findOneBy(["produit" => $recherche]);
        $produits2 = $produitsRepository->findOneBy(["designation" => $recherche]);
        $projet = $projetRepository->findOneBy(["nom" => $recherche]);
        $client = $clientsRepository->findOneBy(["nom" => $recherche]);
        $stock = new Stocks();
        //$recherche = '';
        $pagination = $paginator->paginate(
            $stocksRepository->findRecherche($recherche, $produits1, $produits2, $projet, $client), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render('gestion_stocks/historiques.html.twig',[
            'stocks' => $pagination,
            'recherche' => $recherche,
        ]);
    }    
    /**
     * @Route("/avalider", name="avalider", methods={"GET"})
     */
    public function avalider(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request)
    {
        $etat = new Etats();
        $stock = new Stocks();
        $etat = $etatsRepository->findOneBy(["etat" => "en attente de validation"]);
        $stocks = $stocksRepository->findByGroup($etat->getId());
        $dataReceive = 0;
        foreach($stocks as $sto){
            $dataReceive++;
        }
        return new JsonResponse(['numberAjax' => 200, "dataResponse" => $dataReceive]);  
    }  
    
    
    /**
     * @Route("/quantiterestant", name="quantiterestant", methods={"GET"})
     */
    public function quantiterestant($prod =1, StocksRepository $stocksRepository, Request $request, ProduitsRepository $produitsRepository)
    {
        $produit = new Produits();
        $stock = new Stocks();
        $i = $request->query->getInt('prod');//$this->reste($stock)
        //$i = $prod;
        $produit = $produitsRepository->findBy(["id" => $i]);
        $stock = $stocksRepository->findOneBy(["produit" => $produit]);
        return new JsonResponse(['numberAjax' => 200, "dataResponse" => $this->reste($stock)]);  
    }


    /**
     * @Route("/fileuploadhandler", name="fileuploadhandler")
     */
    public function fileUploadHandler(Request $request) {
        $output = array('uploaded' => false);
        // get the file from the request object
        $file = $request->files->get('file');
        // generate a new filename (safer, better approach)
        // To use original filename, $fileName = $this->file->getClientOriginalName();
        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
     
        // set your uploads directory
        $uploadDir = $this->getParameter('brochures_directory');
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if ($file->move($uploadDir, $fileName)) { 
           $output['uploaded'] = true;
           $output['fileName'] = $fileName;
        }
        return new JsonResponse($output);
    }

    /**
     * @Route("/upload_file/{ref}", name="upload_file", methods={"GET","POST"})
     */
    public function upload_file(Request $request, $ref): Response
    {
        $piecesJointes = new PieceJointe(); 
        $entityManager = $this->getDoctrine()->getManager();   
        $file = $request->files->get('myfile');
        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
        $file->move(
            $this->getParameter('brochures_directory'),
            $fileName
        );
        $reference = $ref;//$request->request->get('piecejointes');
        $daty   = new \DateTime(); //this returns the current date time
        $results = $daty->format('Y-m-d-H-i-s');
        $krr    = explode('-', $results);
        $results = implode("", $krr);
        $piecesJointes->setNomFichier($file->getClientOriginalName()); // mila maka an'ilay reference sy ilay vraie nom de fichier
        $piecesJointes->setNomServer($fileName);
        $piecesJointes->setReferencePJ($reference);
        $entityManager->persist($piecesJointes);
        $entityManager->flush();
        return new JsonResponse(['filesnames' => $results]);
            
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


    /**
     * @Route("/excel", name="excel", methods={"GET","POST"})
     */
    public function excel(StocksRepository $stocksRepository, EtatsRepository $etatsRepository, PaginatorInterface $paginator, Request $request, ProduitsRepository $produitsRepository)
    {
        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        
        
        $entityManager = $this->getDoctrine()->getManager();
        $stock_restant[] = new Stocks();
        $etatsRepository = $entityManager->getRepository(Etats::class);
        $etat = new Etats();
        $etat = $etatsRepository->findOneBy(["id" => 4]);
        $i = 0;
        $mouvement_positif = new Mouvements();
        $mouvement_negatif = new Mouvements();
        $mouvementsRepository = $entityManager->getRepository(Mouvements::class);
        $mouvement_positif = $mouvementsRepository->findOneBy(["id" => 1]);
        $mouvement_negatif = $mouvementsRepository->findOneBy(["id" => 2]);
        $conversionsRepository = $entityManager->getRepository(Conversions::class);
        $total = array();
        $valeur_unite_bas = 0;
        $stock_restant[] = $stocksRepository->findEtat();
        $sheet->setCellValue('A1', 'Produit');
        $sheet->setCellValue('B1', 'Désignation');
        $sheet->setCellValue('C1', 'Quantité restante');
        $i = 2;
        foreach($stocksRepository->findEtat() as $sto){
            $sheet->setCellValue('A'.$i, $sto->getProduit());
            $sheet->setCellValue('B'.$i, $sto->getProduit()->getDesignation());
            $sheet->setCellValue('C'.$i, $this->reste($sto));
            $i++;
        }
        $sheet->setTitle("Prima");
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        $daty   = new \DateTime(); //this returns the current date time
        $results = $daty->format('Y-m-d-H-i-s');
        $krr    = explode('-', $results);
        $results = implode("", $krr);
        // Create a Temporary file in the system
        $fileName = $results.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    
    }

    /**
     * @Route("/", name="cs_base")
     */
    public function rediriger(){
        if(!empty($_SESSION['username']))
        {
            return $this->redirectToRoute('saisie');
        } else 
        {
            return $this->redirectToRoute('app_login');
        }
    }
}
