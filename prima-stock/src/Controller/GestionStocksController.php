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
use App\Form\NouveauType;
use App\Form\EntrerType;
use App\Form\SortieType;
use App\Form\ModifierType;
use App\Form\ProduitsType;
use App\Form\ProjetType;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use App\Repository\MouvementsRepository;
use App\Repository\EtatsRepository;
use App\Repository\UnitesRepository;
use App\Repository\ConversionsRepository;
use App\Repository\ProduitsRepository;
use App\Repository\ProjetRepository;
use Knp\Component\Pager\PaginatorInterface;

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
            $i++;
        }
        return $this->render('gestion_stocks/validation.html.twig',[
            'stocks' => $pagination,
            'reference' => $reference,
            'rests' => $stock_restant,
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
                    if($valeur_unite_bas < $conversion->getValeur()){
                        $valeur_unite_bas = $conversion->getValeur();
                        $unite_bas = $conversion->getUnitesdestinataire();
                    }
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
        return $total.' '.$unite_bas;        
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
            $stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/entrer.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
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
            $stock->setOperateur($userRepository->findOneBy(["id" => 1]));
            $stock->setEtat($etatsrepository->findOneBy(["id" => 1]));
            $stock->setMouvement($mouvement);
            $entityManager->persist($stock);
            $entityManager->flush();

            //return $this->redirectToRoute('nouveau');
        }
        return $this->render('gestion_stocks/sortie.html.twig',[
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }
}
