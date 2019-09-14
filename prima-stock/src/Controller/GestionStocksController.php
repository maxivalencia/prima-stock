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
use App\Form\NouveauType;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use App\Repository\EtatsRepository;

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

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if($form['referencePanier']->getData() != null){
                $stock->setReferencePanier($form['referencePanier']->getData());
            }
            else{
                $daty   = new \DateTime(); //this returns the current date time
                $results = $daty->format('Y-m-d-H-i-s');
                $krr    = explode('-', $results);
                $results = implode("", $krr);
                $stock->setReferencePanier($results);
            }
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
}
